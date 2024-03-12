<?php

namespace App\Providers;

use App\Events\Forms\FormSubmitted;
use App\Events\Models\FormCreated;
use App\Listeners\FailedWebhookListener;
use App\Listeners\Forms\FormCreationConfirmation;
use App\Listeners\Forms\NotifyFormSubmission;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        FormCreated::class => [
            FormCreationConfirmation::class,
        ],
        FormSubmitted::class => [
            NotifyFormSubmission::class
        ],
        WebhookCallFailedEvent::class => [
            FailedWebhookListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
