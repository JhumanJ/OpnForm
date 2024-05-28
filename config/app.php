<?php

use Illuminate\Support\Facades\Facade;

return [

    'front_url' => env('FRONT_URL', null),

    'front_api_secret' => env('FRONT_API_SECRET', null),

    'locales' => [
        'en' => 'EN',
    ],


    'aliases' => Facade::defaultAliases()->merge([
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
