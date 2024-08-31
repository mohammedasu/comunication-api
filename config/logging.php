<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'apilog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/laravel.log'),
            'level' => 'debug',
            'days' => 0,
        ],

        'applog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/app/laravel.log'),
            'level' => 'debug',
            'days' => 0,
        ],
        'cloudwatch' => [
            'driver' => 'custom',
            'name' => env('CLOUDWATCH_LOG_NAME', 'medisage'),
            'region' => env('AWS_CLOUDWATCH_REGION', ''),
            'credentials' => [
                'key' => env('AWS_CLOUDWATCH_ACCESS_KEY_ID', ''),
                'secret' => env('AWS_CLOUDWATCH_SECRET_ACCESS_KEY', '')
            ],
            'stream_name' => env('CLOUDWATCH_LOG_STREAM_NAME', 'communication_app'),
            'retention' => env('CLOUDWATCH_LOG_RETENTION_DAYS', 14),
            'group_name' => env('CLOUDWATCH_LOG_GROUP_NAME', 'medisage-app-logs'),
            'version' => env('CLOUDWATCH_LOG_VERSION', 'latest'),
            'formatter' => \Monolog\Formatter\JsonFormatter::class,
            'disabled' => env('DISABLE_CLOUDWATCH_LOG', false),
            'via' => \Pagevamp\Logger::class,
        ],
    ],
];
