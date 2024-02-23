<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'eu-west-2'),
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
