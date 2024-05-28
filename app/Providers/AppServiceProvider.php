<?php

namespace App\Providers;

use App\Models\Billing\Subscription;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
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

        $this->bootAuth();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->app->environment('local', 'testing') && class_exists(DuskServiceProvider::class)) {
            $this->app->register(DuskServiceProvider::class);
        }
    }

    public function bootAuth()
    {

        \Illuminate\Support\Facades\Gate::define('viewMailcoach', function ($user = null) {
            return $user?->admin;
        });
    }
}
