<?php

namespace App\Providers;

use App\Integrations\OAuth\Drivers\OAuthStripeDriver;
use App\Models\Billing\Subscription;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('filesystems.default') === 'local') {
            Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
                return URL::temporarySignedRoute(
                    'local.temp',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            });
        }

        JsonResource::withoutWrapping();
        Cashier::calculateTaxes();
        Cashier::useSubscriptionModel(Subscription::class);

        if ($this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }

        Validator::includeUnvalidatedArrayKeys();

        Socialite::extend('stripe', function ($app) {
            $config = $app['config']['services.stripe'];
            return Socialite::buildProvider(
                OAuthStripeDriver::class,
                $config
            );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing') && class_exists(DuskServiceProvider::class)) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
