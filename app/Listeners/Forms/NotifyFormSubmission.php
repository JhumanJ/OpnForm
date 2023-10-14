<?php

namespace App\Listeners\Forms;

use App\Models\Forms\Form;
use App\Events\Forms\FormSubmitted;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookServer\WebhookCall;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Service\Forms\FormSubmissionFormatter;
use App\Service\Forms\Webhooks\WebhookHandlerProvider;
use App\Notifications\Forms\FormSubmissionNotification;

class NotifyFormSubmission implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Sends notification to pre-defined emails on form submissions
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FormSubmitted $event)
    {
        $this->sendEmailNotifications($event);

        $this->sendWebhookNotification($event, WebhookHandlerProvider::SIMPLE_WEBHOOK_PROVIDER);
        $this->sendWebhookNotification($event, WebhookHandlerProvider::SLACK_PROVIDER);
        $this->sendWebhookNotification($event, WebhookHandlerProvider::DISCORD_PROVIDER);
        foreach ($event->form->zappierHooks as $hook) {
            $hook->triggerHook($event->data);
        }
    }

    private function sendWebhookNotification(FormSubmitted $event, string $provider)
    {
        WebhookHandlerProvider::getProvider(
            $event->form,
            $event->data,
            $provider
        )->handle();
    }

    /**
     * Sends an email to each email address in the form's notification_emails field
     * @param FormSubmitted $event
     * @return void
     */
    private function sendEmailNotifications(FormSubmitted $event)
    {
        if (!$event->form->is_pro || !$event->form->notifies) return;

        $subscribers = collect(preg_split("/\r\n|\n|\r/", $event->form->notification_emails))->filter(function (
            $email
        ) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        \Log::debug('Sending email notification', [
            'recipients' => $subscribers->toArray(),
            'form_id' => $event->form->id,
            'form_slug' => $event->form->slug,
        ]);
        $subscribers->each(function ($subscriber) use ($event) {
            Notification::route('mail', $subscriber)->notify(new FormSubmissionNotification($event));
        });
    }
}
