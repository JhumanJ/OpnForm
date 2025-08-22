<?php

namespace App\Providers;

use App\Models\Billing\Subscription;
use Aws\S3\S3Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\FilesystemAdapter;
use Laravel\Cashier\Cashier;
use Laravel\Dusk\DuskServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

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

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('stripe', \SocialiteProviders\Stripe\Provider::class);
        });

        /**
         * Register custom Supabase S3 disk
         * Uses AWS SDK middleware to fix duplicate /storage/v1/s3 paths
         */
        Storage::extend('s3', function ($app, $config) {
            $endpoint = rtrim($config['endpoint'], '/');

            $client = new S3Client([
                'version' => 'latest',
                'region' => $config['region'] ?? 'us-east-1',
                'credentials' => [
                    'key' => $config['key'],
                    'secret' => $config['secret'],
                ],
                'endpoint' => $endpoint,
                'use_path_style_endpoint' => true,
            ]);

            // Middleware to strip duplicate /storage/v1/s3
            $handlerList = $client->getHandlerList();
            $handlerList->appendBuild(function (callable $handler) {
                return function ($command, $request) use ($handler) {
                    $uri = (string) $request->getUri();
                    // Remove duplicate /storage/v1/s3 segments
                    $uri = preg_replace('#(/storage/v1/s3)+#', '/storage/v1/s3', $uri);
                    $uri = preg_replace('#(/storage/v1)+#', '/storage/v1', $uri);
                    $request = $request->withUri(new Uri($uri));
                    return $handler($command, $request);
                };
            });

            // Create the Flysystem adapter
            $adapter = new AwsS3V3Adapter($client, $config['bucket']);
            $filesystem = new Filesystem($adapter);
            return new FilesystemAdapter($filesystem, $adapter, $config);
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
