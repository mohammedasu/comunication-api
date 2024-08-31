<?php
return [
    'sender_id' => 'MEDISG',
    'sms_type_transactional' => 'Transactional',
    'entity_id' => '1101665300000029839',
    'app_log_channel' => env('LOG_CHANNEL', 'applog'),
    'whatsapp_url' => 'http://vapi.instaalerts.zone/optin',
    'whatsapp_key' => 'Welcome@1',
	'notification_path' => env('COULDFRONT_BASE_URL', '') . '/notification/',
	'cases_path' => env('COULDFRONT_BASE_URL', '') . '/cases/',
	'videos_path' => env('COULDFRONT_BASE_URL', '') . '/video/',
	'newsletter_path' => env('COULDFRONT_BASE_URL', '') . '/newsletter/',
	'article_path' => env('COULDFRONT_BASE_URL', '') . '/article/',
	'live_event_path' => env('COULDFRONT_BASE_URL') . '/live_event_banner_image/',
    'chunk_size' => '500',
    'storing_chunk_size' => '500',
    'get_member_for_whatsapp' => '500',
    'get_member_for_push' => '1000',
    'days_for_update_UWN' => '1',
	'WEBSITE_URL' => env("WEBSITE_URL", 'https://mymedisage.com'),
]
?>
