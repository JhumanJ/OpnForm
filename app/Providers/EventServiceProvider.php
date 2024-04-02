<?php

namespace App\Providers;

use App\Events\Forms\FormSubmitted;
use App\Events\Models\FormCreated;
use App\Events\Models\FormIntegrationsEventCreated;
use App\Listeners\Forms\FormCreationConfirmation;
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
        FormIntegrationsEventCreated::class => [
            FormIntegrationsEventListener::class,
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
