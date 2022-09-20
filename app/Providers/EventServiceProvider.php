<?php

namespace App\Providers;

use App\Events\Forms\FormSubmitted;
use App\Events\Models\FormCreated;
use App\Listeners\Auth\RegisteredListener;
use App\Listeners\Forms\FormCreationConfirmation;
use App\Listeners\Forms\NotifyFormSubmission;
use App\Listeners\Forms\PostFormDataToWebhook;
use App\Listeners\Forms\SubmissionConfirmation;
use App\Notifications\Forms\FormCreatedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
            FormCreationConfirmation::class
        ],
        FormSubmitted::class => [
            NotifyFormSubmission::class,
            PostFormDataToWebhook::class,
            SubmissionConfirmation::class,
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
