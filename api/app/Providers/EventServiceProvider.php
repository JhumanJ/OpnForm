<?php

namespace App\Providers;

use App\Events\Billing\SubscriptionCreated;
use App\Events\Billing\SubscriptionUpdated;
use App\Events\Forms\FormSubmitted;
use App\Events\Models\FormCreated;
use App\Events\Models\FormIntegrationCreated;
use App\Events\Models\FormIntegrationsEventCreated;
use App\Listeners\Billing\HandleSubscriptionCreated;
use App\Listeners\Billing\RemoveWorkspaceGuestsIfNeeded;
use App\Listeners\Forms\FormCreationConfirmation;
use App\Listeners\Forms\FormIntegrationCreatedHandler;
use App\Listeners\Forms\FormIntegrationsEventListener;
use App\Listeners\Forms\NotifyFormSubmission;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        FormIntegrationCreated::class => [
            FormIntegrationCreatedHandler::class,
        ],
        FormIntegrationsEventCreated::class => [
            FormIntegrationsEventListener::class,
        ],
        SubscriptionCreated::class => [
            HandleSubscriptionCreated::class,
        ],
        SubscriptionUpdated::class => [
            RemoveWorkspaceGuestsIfNeeded::class
        ]
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
