<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
    ],

    'h_captcha' => [
        'site_key' => env('H_CAPTCHA_SITE_KEY'),
        'secret_key' => env('H_CAPTCHA_SECRET_KEY'),
    ],

    'canny' => [
        'api_key' => env('CANNY_API_KEY'),
    ],

    'notion' => [
        'worker' => env('NOTION_WORKER', 'https://notion-forms-worker.notionforms.workers.dev/v1'),
    ],

    'openai' => [
        'api_key' => env('OPEN_AI_API_KEY'),
    ],

    'unsplash' => [
        'access_key' => env('UNSPLASH_ACCESS_KEY'),
        'secret_key' => env('UNSPLASH_SECRET_KEY'),
    ],

    'appsumo' => [
        'client_id' => env('APPSUMO_CLIENT_ID'),
        'client_secret' => env('APPSUMO_CLIENT_SECRET'),
        'api_key' => env('APPSUMO_API_KEY'),
    ],

    'crisp_website_id' => env('CRISP_WEBSITE_ID'),

];
