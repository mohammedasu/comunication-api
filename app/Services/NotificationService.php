<?php

namespace App\Services;

use Carbon\Carbon;
use App\Helpers\UtilityHelper;
use App\Imports\WhatsappImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Traits\WhatsappTrait;
use App\Exceptions\CustomErrorException;
use App\Helpers\AudienceFilterHelper;
use App\Jobs\ProcessDataFilterPushNotification;
use App\Jobs\ProcessDataFilterWhatsapp;
use App\Jobs\ProcessEngagement;
use App\Jobs\ProcessPushNotification;
use App\Jobs\ProcessUtmLinks;
use App\Models\Member;
use App\Models\Universal3;
use App\Repositories\DataFilterRepository;
use App\Repositories\NotificationRepository;

class NotificationService
{
    use WhatsappTrait;

    protected $repository;
    protected $count;
    public function __construct()
    {
        $this->repository               = new NotificationRepository();
        $this->endPointService          = new EndPointService();
        $this->utmLinkService           = new UtmLinkService();
        $this->whatsappLogService       = new WhatsappLogService();
        $this->whatsappTemplateService  = new WhatsappTemplateService();
        $this->memberNotificatioService = new MemberNotificationService();
        $this->memberService            = new MemberService();
        $this->universal3Service        = new Universal3Service();
    }

    public function sendScheduledEngagement()
    {
        try {
            $engagements = $this->repository->getUnprocessedEngagement();
            if (count($engagements) > 0) {
                foreach ($engagements as $engagement) {
                    $this->update(['id' => $engagement->id], ['is_processed' => 2]);
                }
                // $this->engagementQueue($engagements);
                dispatch(new ProcessEngagement($engagements))->delay(Carbon::now()->addSeconds(1));
            }
            return true;
        } catch (\Exception $e) {
            throw new CustomErrorException($e);
        }
    }

    public function update($where, $data)
    {
        return $this->repository->update($where, $data);
    }

    public function engagementQueue($engagements)
    {
        try {
            foreach ($engagements as $engagement) {
                Log::info(['Sending scheduled engagement for engagement id ' => $engagement->id]);

                // For Whatsapp to CSV Members 
                if ($engagement->import_csv && $engagement->notification_type == 'whatsapp') {
                    Excel::import(new WhatsappImport($engagement), 'notification/' . $engagement->import_csv, 's3', \Maatwebsite\Excel\Excel::CSV);
                }
                // For Whatsapp to datafilter Members 
                elseif ($engagement->data_filter_id && $engagement->notification_type == 'whatsapp') {
                    $engagement->content = $engagement->payload['content'] ?? null;
                    $imageUrl = null;
                    if ($engagement->image) {
                        $imageUrl = config('constants.notification_path') . $engagement->image;
                    }
                    $templateData = $this->whatsappTemplateService->getTemplatesForWhatsApp($engagement, $imageUrl);
                    if ($templateData['template']) {
                        $templateData['engagementId'] = $engagement->id;
                        $templateData['dataFilterId'] = $engagement->data_filter_id;
                        // $this->sendWhatsappToDataFilterMembers($templateData);
                        dispatch(new ProcessDataFilterWhatsapp($templateData, 0))->delay(Carbon::now()->addSeconds(1));
                    }
                }
                // For Push Notifications 
                elseif ($engagement->notification_type == 'app_notification') {
                    $engagement->content = $engagement->payload['content'] ?? null;
                    $engagement->notification_title = $engagement->payload['notification_title'] ?? null;
                    $engagement->notification_description = $engagement->payload['notification_description'] ?? null;
                    $templateData = $this->whatsappTemplateService->getTemplatesForAppNotification($engagement);
                    if ($templateData['notification_title']) {
                        $templateData['engagementId']   = $engagement->id;
                        $templateData['deviceType']     = $engagement->device_type;
                        $templateData['dataFilterId']   = $engagement->data_filter_id;
                        // $this->sendPushNotificationToDataFilterMembers($templateData);
                        dispatch(new ProcessDataFilterPushNotification($templateData, 0))->delay(Carbon::now()->addSeconds(1));
                    }
                }
            }
        } catch (\Exception $e) {
            throw new CustomErrorException($e);
        }
    }

    public function sendWhatsappToDataFilterMembers($templateData, $start = 0)
    {
        $data_filter_repository = new DataFilterRepository();
        $universal3Model    = new Universal3();
        $chunkSize          = config('constants.get_member_for_whatsapp');
        $filter_object      = $data_filter_repository->findByMultipleFields(['id' => $templateData['dataFilterId']]);
        $universal_filters  = $filter_object['universal_filters'];
        $member_filters     = $filter_object['member_filters'];
        $live_event_filters = $filter_object['live_event_filters'];
        $filter_data        = AudienceFilterHelper::audienceFilter($universal3Model->query(), $universal_filters, $member_filters, $live_event_filters);

        $members = $filter_data->select(
            'universal3.id',
            'universal3.mobile_number',
            'universal3.whatsapp_number',
            'universal3.member_ref_no',
            'universal3.first_name',
            'universal3.last_name',
            'universal3.country_code',
        )
            // ->whereIn('universal3.mobile_number', ['9870721708', '9773061129'])
            ->whereNotNull(['universal3.mobile_number', 'universal3.first_name', 'universal3.member_ref_no', 'universal3.country_code'])
            ->distinct('universal3.mobile_number')
            ->latest('universal3.id')->skip($start)->take($chunkSize)->get();

        $totalMembers = count($members);
        if ($members->isNotEmpty()) {
            $data = $this->getMemberUtmLinks($members, $templateData);
            dispatch(new ProcessUtmLinks($data['whatAppDataArray'], $data['memberUtmLinks']))->delay(Carbon::now()->addSeconds(1));

            $start = $totalMembers < $chunkSize ? ($start + $totalMembers) : ($start + $chunkSize);
            dispatch(new ProcessDataFilterWhatsapp($templateData, $start))->delay(Carbon::now()->addSeconds(1));
        } else {
            $this->update(
                ['id' => $templateData['engagementId']],
                [
                    'is_processed'  => $start == 0 ? 3 : 1,
                    'count'         => $start
                ]
            );
        }
    }

    public function sendWhatsappToCsvMembers($memberData, $templateData)
    {
        $chunks = $memberData->chunk(config('constants.chunk_size'));
        foreach ($chunks as $value) {
            $data = $this->getMemberUtmLinks($value, $templateData);
            dispatch(new ProcessUtmLinks($data['whatAppDataArray'], $data['memberUtmLinks']))->delay(Carbon::now()->addSeconds(1));
        }
        $this->update(['id' => $templateData['engagementId']], ['is_processed' => 1]);
    }

    public function getMemberUtmLinks($members, $templateData)
    {
        $whatAppDataArray   = [];
        $memberUtmLinks     = [];
        foreach ($members as $mem) {
            if (!isset($mem->mobile_number)) {
                // for csv to convert array into object
                $object = new \stdClass();
                foreach ($mem as $key => $value) {
                    $object->$key = $value;
                }
                $member = $object;
            } else {
                $member = $mem;
            }

            $country_code       = $member->country_code;
            $member_ref_no      = $member->member_ref_no;
            $whatsapp_number    = isset($member->whatsapp_number) ? $member->whatsapp_number : $member->mobile_number;
            $mobile_number      = str_replace('+', '', $country_code) . $whatsapp_number;
            $name               = isset($member->fname) ? $member->fname : $member->first_name;
            if (isset($templateData['action_type']) && ($templateData['action_type'] == 'video' || $templateData['action_type'] == 'case' || $templateData['action_type'] == 'newsletter' || $templateData['action_type'] == 'newsarticle')) {

                // generating UTM link
                $utmId          = $templateData['action_id'] ?? 0;
                $utmCampaign    = $templateData['engagementId'] ?? "";
                $utmContentType = $templateData['action_type'] ?? "";
                $utmMedium      = "whatsapp";
                $refId          = uniqid();
                $utmLink        = $templateData['urlLink'] . "?ref_code=" . $refId .
                    "&utm_source=GodMode&utm_medium=" . $utmMedium .
                    "&utm_campaign=" . $utmCampaign .
                    "&utm_id=" . $utmId . "&utm_content=" . $utmContentType . "";
                $redirectedUrl  = config('constants.WEBSITE_URL') . '/share/' . $refId;

                array_push($memberUtmLinks, [
                    "digimr_doctor_id"  => $member_ref_no ?? 0,
                    "universal_doctor_id" => $member_ref_no ?? 0,
                    "project_id"        => 0,
                    "digimr_id"         => 0,
                    "ref_id"            => $refId,
                    "utm_campaign"      => $utmCampaign,
                    "utm_medium"        => $utmMedium,
                    "utm_id"            => $utmId,
                    "utm_link"          => $utmLink,
                    "wave_number"       => 1,
                    'created_at'        => Now(),
                    'updated_at'        => Now(),
                ]);
                $templateData['contentParams'][2] = ' click on this link to view on website ' . ($redirectedUrl);
            }
            $templateData['contentParams'][0] = $name;
            $data['contentParams']  = (object) $templateData['contentParams'];
            $data['templateId']     = $templateData['template']->template_id;
            $data['templatePrimaryId'] = $templateData['template']->id;
            $data['mobileNumber']   = '+' . $mobile_number;
            $data['whatsappNumber'] = $whatsapp_number;
            $data['imageLink']      = $templateData['image'] ?? null;
            $data['engagementId']   = $templateData['engagementId'] ?? null;
            $data['member_id']      = $member_ref_no ?? 0;

            array_push($whatAppDataArray, $data);
        }
        return [
            'memberUtmLinks'    => $memberUtmLinks,
            'whatAppDataArray'  => $whatAppDataArray
        ];
    }

    public function whatsappQueue($whatAppData)
    {
        try {
            $whatAppLogArray = [];
            foreach ($whatAppData as $value) {
                $responseBody = $this->sendWhatsappMessage($value);
                $responseBody['whatsappNumber'] = $value['whatsappNumber'];
                array_push($whatAppLogArray, [
                    'type'                  => $value['templateId'],
                    'whatsapp_template_id'  => $value['templatePrimaryId'],
                    'reference_type'        => 'member',
                    'reference_id'          => $value['member_id'],
                    'engagement_id'         => $value['engagementId'],
                    'response'              => json_encode($responseBody, true),
                    'mid'                   => isset($responseBody['mid']) ? $responseBody['mid'] : null,
                    'created_at'            => Now(),
                    'updated_at'            => Now(),
                ]);
            }
            return $whatAppLogArray;
        } catch (\Exception $e) {
            throw new CustomErrorException($e);
        }
    }

    public function sendPushNotificationToDataFilterMembers($templateData, $start = 0)
    {
        $deviceType = 'all';
        if ($templateData['deviceType'] && $templateData['deviceType'] != 'all') {
            $deviceType = $templateData['deviceType'];
        }

        $chunkSize = config('constants.get_member_for_push');
        if ($templateData['dataFilterId']) {
            $universal3Model    = new Universal3();
            $data_filter_repository = new DataFilterRepository();
            $filter_object  = $data_filter_repository->findByMultipleFields(['id' => $templateData['dataFilterId']]);
            $universal_filters  = $filter_object['universal_filters'];
            $member_filters     = $filter_object['member_filters'];
            $live_event_filters = $filter_object['live_event_filters'];
            $filter_data        = AudienceFilterHelper::audienceFilter($universal3Model->query(), $universal_filters, $member_filters, $live_event_filters, 'push_notification');
        } else {
            $memberModel = new Member();
            $filter_data = $memberModel->query();
        }
        $members = $filter_data->select(
            'members.id',
            'members.device_token',
            'members.device_type',
            'members.member_ref_no',
            'members.mobile_number'
        )
            ->distinct('members.mobile_number')
            ->whereNotNull(['members.member_ref_no', 'members.device_token', 'members.device_type'])
            ->when($deviceType != 'all', function ($q) use ($deviceType) {
                return $q->where('members.device_type', $deviceType);
            })
            // ->whereIn('members.mobile_number', ['9870721708', '8169188810'])
            ->latest('members.id')->skip($start)->take($chunkSize)->get();
        $totalMembers = count($members);

        if ($members->isNotEmpty()) {
            $androidMembers = $members->filter(function ($item) {
                return $item->device_type == 'android';
            })->values();

            $iosMembers = $members->filter(function ($item) {
                return $item->device_type == 'ios';
            })->values();

            $memberData = [
                'androidMemberTokens'   => $androidMembers->pluck('device_token')->toArray(),
                'androidMemberIds'      => $androidMembers->pluck('id')->toArray(),
                'iosMemberTokens'       => $iosMembers->pluck('device_token')->toArray(),
                'iosMemberIds'          => $iosMembers->pluck('id')->toArray()
            ];
            if (!empty($memberData['iosMemberTokens']) || !empty($memberData['androidMemberTokens'])) {
                // $this->pushNotificationQueue($memberData, $templateData);
                dispatch(new ProcessPushNotification($memberData, $templateData))->delay(Carbon::now()->addSeconds(1));
            }
            $start = $totalMembers < $chunkSize ? ($start + $totalMembers) : ($start + $chunkSize);
            // $this->sendPushNotificationToDataFilterMembers($filterDataQuery, $templateData, $start);
            dispatch(new ProcessDataFilterPushNotification($templateData, $start))->delay(Carbon::now()->addSeconds(1));
        } else {
            $this->update(
                ['id' => $templateData['engagementId']],
                [
                    'is_processed'  => $start == 0 ? 3 : 1,
                    'count'         => $start,
                ]
            );
        }
    }

    public function pushNotificationQueue($memberData, $templateData)
    {
        if (!empty($memberData['androidMemberTokens'])) {
            if (!empty($memberData['androidMemberIds']) && isset($templateData['engagementId'])) {
                $this->createMemberNotifications($memberData['androidMemberIds'], $templateData['engagementId']);
            }
            $device_token = $memberData['androidMemberTokens'];
            $chunks = array_chunk($device_token, config('constants.chunk_size'));
            foreach ($chunks as $chunk) {
                $param = [
                    'title'             => $templateData['notification_title'],
                    'message'           => $templateData['notification_description'],
                    'device_token'      => $chunk,
                    'auth_key'          => env("FCM_ANDROID_KEY"),
                    'action_type'       => $templateData['action_type'],
                    'action_id'         => $templateData['action_id'],
                    'device_type'       => 'android',
                    'notification_id'   => $templateData['engagementId']
                ];
                $this->sendFCM($param);
            }
        }

        if (!empty($memberData['iosMemberTokens'])) {
            if (!empty($memberData['iosMemberIds']) && isset($templateData['engagementId'])) {
                $this->createMemberNotifications($memberData['iosMemberIds'], $templateData['engagementId']);
            }
            $device_token = $memberData['iosMemberTokens'];
            $chunks = array_chunk($device_token, config('constants.chunk_size'));
            foreach ($chunks as $chunk) {
                $param = [
                    'title'             => $templateData['notification_title'],
                    'message'           => $templateData['notification_description'],
                    'device_token'      => $chunk,
                    'auth_key'          => env("FCM_IOS_KEY"),
                    'action_type'       => $templateData['action_type'],
                    'action_id'         => $templateData['action_id'],
                    'device_type'       => 'ios',
                    'notification_id'   => $templateData['engagementId']
                ];
                $this->sendFCM($param);
            }
        }
    }

    public function sendFCM(array $request)
    {
        $url = env("FCM_URL");
        $body_array = array("body" => $request['message'], "title" => $request['title'], "icon" => "myicon");
        $app_url = config('constants.WEBSITE_URL');
        $live_event_link = $app_url . '/liveEvent/';

        if ($request['action_type']) {

            if ($request['device_type'] == 'ios') {
                $body_array = array("body" => $request['message'], "title" => $request['title'], "icon" => "myicon", "mutable-content" => true, "content_available" => true);
            } else {
                $click_action = 'com.mymedisage.medisageapp.modules.HomeActivity';

                $body_array = array(
                    "body" => $request['message'], "title" => $request['title'], "icon" => "myicon",
                    "click_action" => $click_action
                );
            }

            $fields = array(
                'registration_ids' => $request['device_token'], 'notification' => $body_array,
                "data" => array(
                    "id" => $request['action_id'], "action" => $request['action_type'],
                    "notification_id" => $request['notification_id'],
                    "live_event_link" => ($request['action_type'] == 'live_event') ? $live_event_link : ''
                )
            );
        } else {
            if ($request['device_type'] == 'ios') {
                $body_array = array("body" => $request['message'], "title" => $request['title'], "icon" => "myicon", "mutable-content" => true, "content_available" => true);

                $fields = array('registration_ids' => $request['device_token'], 'notification' => $body_array);
            } else {
                $fields = array('registration_ids' => $request['device_token'], 'notification' => $body_array);
            }
        }

        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . $request['auth_key'],
            'Content-Type: application/json'
        );

        return UtilityHelper::postRequestToCommunication($url, $fields, $headers);
    }

    public function createMemberNotifications($member_ids, $id, $notification_type = 'sent')
    {
        if (isset($member_ids)) {
            $data = array();
            foreach ($member_ids as $member_id) {
                array_push($data, [
                    'member_id'         => $member_id,
                    'notification_id'   => $id,
                    'type'              => $notification_type,
                    'created_at'        => Carbon::now()
                ]);
            }

            foreach (array_chunk($data, config('constants.storing_chunk_size')) as $t) {
                $this->memberNotificatioService->store($t);
            }
        }

        return true;
    }
}
