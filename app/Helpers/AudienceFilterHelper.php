<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AudienceFilterHelper
{

    public static function universal3Filter($filter_data, $universal_filters)
    {
        if (isset($universal_filters['countries_selected']) && $universal_filters['countries_selected'] != null && $universal_filters['countries_selected'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.country', $universal_filters['countries_selected']);
        }

        if (isset($universal_filters['countries_negative_selected']) && $universal_filters['countries_negative_selected'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.country', $universal_filters['countries_negative_selected']);
        }

        if (isset($universal_filters['city_selected']) && $universal_filters['city_selected'] != null && $universal_filters['city_selected'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.city', $universal_filters['city_selected']);
        }

        if (isset($universal_filters['city_negative_selected']) && $universal_filters['city_negative_selected'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.city', $universal_filters['city_negative_selected']);
        }

        if (isset($universal_filters['member_type']) && $universal_filters['member_type'] != null && $universal_filters['member_type'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.user_status', $universal_filters['member_type']);
        }

        if (isset($universal_filters['member_negative_type']) && $universal_filters['member_negative_type'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.user_status', $universal_filters['member_negative_type']);
        }

        if (isset($universal_filters['tier_selected']) && $universal_filters['tier_selected'] != null && $universal_filters['tier_selected'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.tier', $universal_filters['tier_selected']);
        }

        if (isset($universal_filters['tier_negative_selected']) && $universal_filters['tier_negative_selected'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.tier', $universal_filters['tier_negative_selected']);
        }

        if (isset($universal_filters['zone_selected']) && $universal_filters['zone_selected'] != null && $universal_filters['zone_selected'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.zone', $universal_filters['zone_selected']);
        }

        if (isset($universal_filters['zone_negative_selected']) && $universal_filters['zone_negative_selected'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.zone', $universal_filters['zone_negative_selected']);
        }

        if (isset($universal_filters['state_selected']) && $universal_filters['state_selected'] != null && $universal_filters['state_selected'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.state', $universal_filters['state_selected']);
        }

        if (isset($universal_filters['state_negative_selected']) && $universal_filters['state_negative_selected'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.state', $universal_filters['state_negative_selected']);
        }

        if (isset($universal_filters['sms_active_status']) && $universal_filters['sms_active_status'] != null && $universal_filters['sms_active_status'][0] != null) {

            if (in_array('null', $universal_filters['sms_active_status'])) {
                $key = array_search('null', $universal_filters['sms_active_status']);
                unset($universal_filters['sms_active_status'][$key]);

                if (count($universal_filters['sms_active_status']) > 0) {
                    $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
                        $query->whereNull('universal3.sms_active')->orWhereIn('universal3.sms_active', $universal_filters['sms_active_status']);
                    });
                } else {
                    $filter_data = $filter_data->whereNull('sms_active');
                }
            } else {
                $filter_data = $filter_data->whereIn('universal3.sms_active', $universal_filters['sms_active_status']);
            }
            // $filter_data = $filter_data->whereIn('universal3.sms_active', $universal_filters['sms_active_status']);
        }

        if (isset($universal_filters['sms_active_negative_status']) && $universal_filters['sms_active_negative_status'] != null) {

            if (in_array('null', $universal_filters['sms_active_negative_status'])) {
                $key = array_search('null', $universal_filters['sms_active_negative_status']);
                unset($universal_filters['sms_active_negative_status'][$key]);

                if (count($universal_filters['sms_active_negative_status']) > 0) {
                    $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
                        $query->whereNotNull('universal3.sms_active')->orWhereNotIn('universal3.sms_active', $universal_filters['sms_active_negative_status']);
                    });
                } else {
                    $filter_data = $filter_data->whereNotNull('sms_active');
                }
            } else {
                $filter_data = $filter_data->whereNotIn('universal3.sms_active', $universal_filters['sms_active_negative_status']);
            }

            // $filter_data = $filter_data->whereNotIn('universal3.sms_active', $universal_filters['sms_active_negative_status']);
        }

        if (isset($universal_filters['speciality_selected']) && $universal_filters['speciality_selected'] != null && $universal_filters['speciality_selected'][0] != null) {
            $filter_data = $filter_data->whereIn('universal3.speciality', $universal_filters['speciality_selected']);
        }

        if (isset($universal_filters['speciality_negative_selected']) && $universal_filters['speciality_negative_selected'] != null) {
            $filter_data = $filter_data->whereNotIn('universal3.speciality', $universal_filters['speciality_negative_selected']);
        }

        if (isset($universal_filters['whatsapp_active_status']) && $universal_filters['whatsapp_active_status'] != null && $universal_filters['whatsapp_active_status'][0] != null) {

            if (in_array('null', $universal_filters['whatsapp_active_status'])) {
                $key = array_search('null', $universal_filters['whatsapp_active_status']);
                unset($universal_filters['whatsapp_active_status'][$key]);

                if (count($universal_filters['whatsapp_active_status']) > 0) {
                    $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
                        $query->whereNull('universal3.is_whatsapp_active')->orWhereIn('universal3.is_whatsapp_active', $universal_filters['whatsapp_active_status']);
                    });
                } else {
                    $filter_data = $filter_data->whereNull('is_whatsapp_active');
                }
            } else {
                $filter_data = $filter_data->whereIn('universal3.is_whatsapp_active', $universal_filters['whatsapp_active_status']);
            }

            //$filter_data = $filter_data->whereIn('universal3.is_whatsapp_active', $universal_filters['whatsapp_active_status']);
        }

        if (isset($universal_filters['whatsapp_active_negative_status']) && $universal_filters['whatsapp_active_negative_status'] != null) {

            if (in_array('null', $universal_filters['whatsapp_active_negative_status'])) {
                $key = array_search('null', $universal_filters['whatsapp_active_negative_status']);
                unset($universal_filters['whatsapp_active_negative_status'][$key]);

                if (count($universal_filters['whatsapp_active_negative_status']) > 0) {
                    $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
                        $query->whereNotNull('universal3.is_whatsapp_active')->orWhereNotIn('universal3.is_whatsapp_active', $universal_filters['whatsapp_active_negative_status']);
                    });
                } else {
                    $filter_data = $filter_data->whereNotNull('is_whatsapp_active');
                }
            } else {
                $filter_data = $filter_data->whereNotIn('universal3.is_whatsapp_active', $universal_filters['whatsapp_active_negative_status']);
            }

            //$filter_data = $filter_data->whereNotIn('universal3.is_whatsapp_active', $universal_filters['whatsapp_active_negative_status']);
        }

        if (isset($universal_filters['digiMR_status']) && $universal_filters['digiMR_status'] != null && $universal_filters['digiMR_status'][0] != null) {
            if (in_array('null', $universal_filters['digiMR_status'])) {
                $key = array_search('null', $universal_filters['digiMR_status']);
                unset($universal_filters['digiMR_status'][$key]);

                if (count($universal_filters['digiMR_status']) > 0) {
                    $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
                        $query->whereNull('universal3.digiMR_status')->orWhereIn('universal3.digiMR_status', $universal_filters['digiMR_status']);
                    });
                } else {
                    $filter_data = $filter_data->whereNull('digiMR_status');
                }
            } else {
                $filter_data = $filter_data->whereIn('universal3.digiMR_status', $universal_filters['digiMR_status']);
            }
        }

        if (isset($universal_filters['digiMR_negative_status']) && $universal_filters['digiMR_negative_status'] != null) {


            if (in_array('null', $universal_filters['digiMR_negative_status'])) {
                $key = array_search('null', $universal_filters['digiMR_negative_status']);
                unset($universal_filters['digiMR_negative_status'][$key]);

                if (count($universal_filters['digiMR_negative_status']) > 0) {
                    $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
                        $query->whereNotNull('universal3.digiMR_status')->orWhereNotIn('universal3.digiMR_status', $universal_filters['digiMR_negative_status']);
                    });
                } else {
                    $filter_data = $filter_data->whereNotNull('digiMR_status');
                }
            } else {
                $filter_data = $filter_data->whereNotIn('universal3.digiMR_status', $universal_filters['digiMR_negative_status']);
            }
            // if (count($universal_filters['digiMR_negative_status']) > 0) {
            //     $filter_data = $filter_data->whereNotIn('universal3.digiMR_status', $universal_filters['digiMR_negative_status']);
            // }
            // $filter_data = $filter_data->whereNotIn('universal3.digiMR_status', $universal_filters['digiMR_negative_status'])->orwhereNull('universal3.digiMR_status');
            // $filter_data = $filter_data->where(function ($query) use ($universal_filters) {
            //     $query->whereNull('universal3.digiMR_status')->orWhereNotIn('universal3.digiMR_status', $universal_filters['digiMR_negative_status']);
            // });
        }

        if (isset($universal_filters['last_active_since']) && $universal_filters['last_active_since'] != null && $universal_filters['last_active_since'] > 0) {
            $filter_data = $filter_data->where('universal3.last_activity_date', '>', Carbon::now()->subDays($universal_filters['last_active_since'])->toDateTimeString());
        }
        return $filter_data;
    }

    public static function memberFilter($filter_data, $member_filters, $request_for)
    {
        $check_prime = false;
        if (isset($member_filters['member_is_prime']) && $member_filters['member_is_prime'] != null) {
            $check_prime = filter_var($member_filters['member_is_prime'], FILTER_VALIDATE_BOOLEAN);
        }

        if ($request_for == 'push_notification') {
            $filter_data = $filter_data->join('members', 'members.member_ref_no', '=', 'universal3.member_ref_no');
        }

        if (isset($member_filters) && $member_filters != null && ((isset($member_filters['answered_case']) && $member_filters['answered_case'] != null) || (isset($member_filters['video_watched']) && $member_filters['video_watched'] != null)
            || (isset($member_filters['forum_subscription']) && $member_filters['forum_subscription'] != null) || (isset($member_filters['member_is_prime']) && $member_filters['member_is_prime'] != null && $check_prime))) {

            if ($request_for == 'all') {
                $filter_data = $filter_data->join('members', 'members.member_ref_no', '=', 'universal3.member_ref_no');
            }

            if ((isset($member_filters['answered_case']) && $member_filters['answered_case'] != null)) {
                $filter_data = $filter_data->join('case_members as CM', 'members.id', '=', 'CM.member_id');
                $answered_case_check = filter_var($member_filters['answered_case_check'], FILTER_VALIDATE_BOOLEAN);

                if ($answered_case_check == 1) {
                    foreach ($member_filters['answered_case'] as $case_id) {
                        $filter_data = $filter_data->where('CM.case_id', $case_id);
                    }
                } else {
                    $filter_data = $filter_data->whereIn('CM.case_id', $member_filters['answered_case']);
                }
            }

            if ((isset($member_filters['video_watched']) && $member_filters['video_watched'] != null)) {

                $filter_data = $filter_data->join('member_video_histories as MVH', 'members.id', '=', 'MVH.member_id');
                $video_watched_check = filter_var($member_filters['video_watched_check'], FILTER_VALIDATE_BOOLEAN);

                if ($video_watched_check == 1) {
                    foreach ($member_filters['video_watched'] as $video_id) {
                        $filter_data = $filter_data->where('MVH.video_id', $video_id);
                    }
                } else {
                    $filter_data = $filter_data->whereIn('MVH.video_id', $member_filters['video_watched']);
                }
            }

            if ((isset($member_filters['forum_subscription']) && $member_filters['forum_subscription'] != null)) {
                $filter_data = $filter_data->join('member_forum_subscriptions as MFS', 'members.id', '=', 'MFS.member_id');
                $forum_subscription_check = filter_var($member_filters['forum_subscription_check'], FILTER_VALIDATE_BOOLEAN);

                if ($forum_subscription_check == 1) {
                    foreach ($member_filters['forum_subscription'] as $forum_id) {
                        $filter_data = $filter_data->where('MFS.partner_division_id', $forum_id);
                    }
                } else {
                    $filter_data = $filter_data->whereIn('MFS.partner_division_id', $member_filters['forum_subscription']);
                }
            }
            if ((isset($member_filters['member_is_prime']) && $member_filters['member_is_prime'] != null)) {
                $check_prime = filter_var($member_filters['member_is_prime'], FILTER_VALIDATE_BOOLEAN);
                if ($check_prime == 1) {
                    $filter_data = $filter_data->where('members.is_prime', 1);
                }
            }
        }
        return $filter_data;
    }
    public static function liveEventFilter($filter_data, $live_event_filters, $request_for)
    {
        if (isset($live_event_filters) && $live_event_filters != null && ((isset($live_event_filters['live_event_visited']) && $live_event_filters['live_event_visited'] != null) || (isset($live_event_filters['live_event_registered']) && $live_event_filters['live_event_registered'] != null)
            || (isset($live_event_filters['live_event_partner']) && $live_event_filters['live_event_partner'] != null)
            || (isset($live_event_filters['live_event_partner_division_id']) && $live_event_filters['live_event_partner_division_id'] != null))) {

            $filter_data = $filter_data->join('live_event_members as LEM', 'LEM.mobile_number', '=', 'universal3.mobile_number');

            if (isset($live_event_filters['live_event_visited']) && $live_event_filters['live_event_visited'] != null) {
                $live_event_visited_check = filter_var($live_event_filters['live_event_visited_check'], FILTER_VALIDATE_BOOLEAN);

                if ($live_event_visited_check == 1) {
                    foreach ($live_event_filters['live_event_visited'] as $live_event_id) {
                        $filter_data = $filter_data->where('LEM.link_id', $live_event_id)->where('LEM.visited_during_session', true);
                    }
                } else {
                    $filter_data = $filter_data->whereIn('LEM.link_id', $live_event_filters['live_event_visited'])->where('LEM.visited_during_session', true);
                }
            }
            if (isset($live_event_filters['live_event_registered']) && $live_event_filters['live_event_registered'] != null) {
                $live_event_registered_check = filter_var($live_event_filters['live_event_registered_check'], FILTER_VALIDATE_BOOLEAN);

                if ($live_event_registered_check == 1) {
                    foreach ($live_event_filters['live_event_registered'] as $live_event_id) {
                        $filter_data = $filter_data->where('LEM.link_id', $live_event_id);
                    }
                } else {
                    $filter_data = $filter_data->whereIn('LEM.link_id', $live_event_filters['live_event_registered']);
                }
            }

            if ((isset($live_event_filters['live_event_partner']) && $live_event_filters['live_event_partner'] != null) || (isset($live_event_filters['live_event_partner_division_id']) && $live_event_filters['live_event_partner_division_id'] != null)) {

                $filter_data = $filter_data->join('live_events as LE', 'LEM.link_id', '=', 'LEM.id');

                if ($live_event_filters['live_event_partner'] != null) {
                    $filter_data = $filter_data->whereIn('LE.partner_id', $live_event_filters['live_event_partner']);
                }
                if ($live_event_filters['live_event_partner_division_id'] != null) {
                    $filter_data = $filter_data->whereIn('LE.partner_division_id', $live_event_filters['live_event_partner_division_id']);
                }
            }
        }
        return $filter_data;
    }
    public static function  audienceFilter($filter_data, $universal_filters, $member_filters, $live_event_filters, $request_for = 'all')
    {
        Log::info('AudienceFilterHelper | Data Filter');
        $filter_data = self::universal3Filter($filter_data, $universal_filters);
        $filter_data = self::memberFilter($filter_data, $member_filters, $request_for);
        $filter_data = self::liveEventFilter($filter_data, $live_event_filters, $request_for);
        return $filter_data;
    }
}
