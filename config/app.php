<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'front_url' => env('FRONT_URL', null),

    'front_api_secret' => env('FRONT_API_SECRET', null),

    'locales' => [
        'en' => 'EN',
    ],

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Laravel Framework Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\VaporUiServiceProvider::class,
        App\Providers\ModelStatsServiceProvider::class,
        App\Providers\PurifySetupProvider::class,

        /*
        * Package Service Providers...
        */
        Propaganistas\LaravelDisposableEmail\DisposableEmailServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
