<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomErrorException;
use App\Repositories\WhatsappTemplateRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WhatsappTemplateService
{
    protected $repository;

    public function __construct()
    {
        $this->repository       = new WhatsappTemplateRepository();
        $this->caseService      = new CaseService();
        $this->newsletterService = new NewsletterService();
        $this->videoService     = new VideoService();
    }

    public function getTemplatesForWhatsApp($request, $imageUrl = null)
    {
        Log::info('WhatsappTemplateService | getTemplates', ['request data for engagement id ' => $request->id, 'image' => $imageUrl]);
        try {
            if ($request->action_type == 'custom' && $request->content != null) {
                $contentArray[0] = "doctor_name";
                $obj = explode('<br>', $request->content);

                foreach ($obj as $key => $value) {
                    array_push($contentArray, $value);
                }

                $templateString = 'subscription';
                if ($imageUrl != null) {
                    $templateString = 'subscription_media';
                }
                $template = $this->repository->getTemplates($templateString, count($contentArray), $imageUrl);
                return [
                    'contentParams' => $contentArray,
                    'template'      => $template,
                    'image'         => $imageUrl,
                    'action_type'   => 'custom',
                    'action_id'    => 0,
                    'urlLink'       => null
                ];
            } else if ($request->action_type == 'video' || $request->action_type == 'case' || $request->action_type == 'newsletter' || $request->action_type == 'newsarticle' || $request->action_type == 'live_event') {
                $action_id = $request->action_id;
                $action_type = $request->action_type;
                $template_id = "subscription_media_2_variable";

                $image_exist = false;
                $image_link = "";
                $app_url = config('constants.WEBSITE_URL');

                if ($action_type == 'case') {
                    $content_data = $this->caseService->show($action_id);
                    $case_item_data = $content_data->case_item ?? null;

                    if ($case_item_data && $case_item_data->image_name) {
                        $exists = Storage::disk('s3')->exists('/cases/' . $case_item_data->image_name);
                        if ($exists) {
                            $image_link = config('constants.cases_path') . $case_item_data->image_name;
                            $image_exist = true;
                        }
                    }
                    $urlLink = $app_url . "/cases/" . $action_id;
                } else if ($action_type == 'newsletter') {
                    $content_data = $this->newsletterService->show($action_id);
                    $image = $content_data->image_name ?? null;
                    $forum_name = !empty($content_data->forumName) ? $content_data->forumName->link_name : null;
                    $url_link = $content_data->url_link ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('/newsletter/' . $image);
                        if ($exists) {
                            $image_link = config('constants.newsletter_path') . $image;
                            $image_exist = true;
                        }
                    }
                    $urlLink = $app_url . "/newsletters/" . $forum_name . "/" . $url_link;
                } else if ($action_type == 'video') {
                    $content_data = $this->videoService->show(['id' => $action_id]);
                    $image = $content_data->image_name ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('video/' . $image);
                        if ($exists) {
                            $image_link = config('constants.videos_path') . $image;
                            $image_exist = true;
                        }
                    }
                    $urlLink = $app_url . "/video/" . base64_encode($action_id);
                } else if ($action_type == 'newsarticle') {
                    $articleService = new ArticleService();
                    $content_data = $articleService->show(['id' => $action_id]);
                    $image = $content_data->card_image ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('article/' . $image);
                        if ($exists) {
                            $image_link = config('constants.article_path') . $image;
                            $image_exist = true;
                        }
                    }
                    $urlLink = $app_url . "/news/" . $action_id;
                } else if ($action_type == 'live_event') {
                    $liveEventService = new LiveEventService();
                    $content_data = $liveEventService->show(['id' => $action_id]);
                    $image = $content_data->banner_image ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('live_event_banner_image/' . $image);
                        if ($exists) {
                            $image_link = config('constants.live_event_path') . $image;
                            $image_exist = true;
                        }
                    }
                    $urlLink = $app_url . "/liveEvent/" . $content_data->link_id;
                }

                if (!$image_exist) {
                    $template_id = "subscription_2_variable";   // 2 argument in payload without image
                }

                $template = $this->repository->fetch(['template_id' => $template_id]);
                $app_download_link = DB::table('common_configs')->where('config_name', 'app_download_link')->select('value')->first();
                $contentArray[] = "doctor_name";
                if ($action_type == 'case') {
                    $contentArray[] = 'An interesting patient case has been posted titled, *' . $content_data->title . '* The doctor community seeks your valuable opinion on this case. Respond via the Medisage app ' . $app_download_link->value;
                } else if ($action_type == "newsletter") {
                    $contentArray[] = 'Here is a newsletter that discusses *' . $content_data->title . '*. Read on the MediSage app ' . $app_download_link->value;
                } else if ($action_type == "newsarticle") {
                    $contentArray[] = 'Here is a news article that discusses *' . $content_data->header . '*. Read on the MediSage app ' . $app_download_link->value;
                } else if ($action_type == "live_event") {
                    $contentArray[] = 'Here is a live event that discusses *' . $content_data->header . '*. Read on the MediSage app ' . $app_download_link->value;
                } else {
                    $contentArray[] = 'Here is a video that discusses *' . $content_data->title . '*. Watch on the MediSage app ' . $app_download_link->value;
                }
                $contentArray[] = 'Or click this link to watch on website ' . $urlLink;
                return [
                    'contentParams' => $contentArray,
                    'template'      => $template,
                    'image'         => $image_link,
                    'action_type'   => $action_type,
                    'action_id'     => $action_id,
                    'urlLink'       => $urlLink
                ];
            }
        } catch (\Exception $e) {
            throw new CustomErrorException($e);
        }
    }

    public function getTemplatesForAppNotification($request)
    {
        Log::info('WhatsappTemplateService | getTemplatesForAppNotification', ['request data for engagement id ' => $request->id]);
        try {
            $image_exist = false;
            $image_link = "";
            if ($request->action_type == 'video' || $request->action_type == 'case' || $request->action_type == 'newsletter' || $request->action_type == 'newsarticle' || $request->action_type == 'live_event') {
                $action_id = $request->action_id;
                $action = $action_type = $request->action_type;

                if ($action_type == 'case') {
                    $content_data = $this->caseService->show($action_id);
                    $case_item_data = $content_data->case_item ?? null;
                    $action = 'cases';
                    if ($case_item_data && $case_item_data->image_name) {
                        $exists = Storage::disk('s3')->exists('/cases/' . $case_item_data->image_name);
                        if ($exists) {
                            $image_link = config('constants.cases_path') . $case_item_data->image_name;
                        }
                    }
                } else if ($action_type == 'newsletter') {
                    $content_data = $this->newsletterService->show($action_id);
                    $image = $content_data->image_name ?? null;
                    $action = 'news_letter';
                    $action_id = config('constants.document_path') . $content_data->file_name;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('/newsletter/' . $image);
                        if ($exists) {
                            $image_link = config('constants.newsletter_path') . $image;
                        }
                    }
                } else if ($action_type == 'video') {
                    $content_data = $this->videoService->show(['id' => $action_id]);
                    $image = $content_data->image_name ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('video/' . $image);
                        if ($exists) {
                            $image_link = config('constants.videos_path') . $image;
                        }
                    }
                } else if ($action_type == 'newsarticle') {
                    $articleService = new ArticleService();
                    $content_data = $articleService->show(['id' => $action_id]);
                    $action = 'news_article';
                    $image = $content_data->card_image ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('article/' . $image);
                        if ($exists) {
                            $image_link = config('constants.article_path') . $image;
                        }
                    }
                } else if ($action_type == 'live_event') {
                    $liveEventService = new LiveEventService();
                    $content_data = $liveEventService->show(['id' => $action_id]);
                    $action_id = $content_data->link_id;
                    $image = $content_data->banner_image ?? null;
                    if ($image) {
                        $exists = Storage::disk('s3')->exists('live_event_banner_image/' . $image);
                        if ($exists) {
                            $image_link = config('constants.live_event_path') . $image;
                        }
                    }
                }

                return [
                    'notification_title'        => $request->notification_title,
                    'notification_description'  => $request->notification_description,
                    'image'                     => $image_link,
                    'action_type'               => $action,
                    'action_id'                 => $action_id,
                ];
            }
            throw new CustomErrorException(null, "Invalid Action Type / ID.", 500);
        } catch (\Exception $e) {
            throw new CustomErrorException($e);
        }
    }
}
