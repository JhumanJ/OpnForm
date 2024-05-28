<?php

use Monolog\Handler\StreamHandler;

return [

    'channels' => [
        'slack' => [
            'enabled' => env('LOG_SLACK_WEBHOOK_URL') ? true : false,
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'OpenForm Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'level' => env('LOG_LEVEL'),
            'with' => [
                'stream' => 'php://stderr',
                'level' => env('LOG_LEVEL'),
            ],
        ],
    ],

];
