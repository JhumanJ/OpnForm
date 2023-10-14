<?php

namespace App\Listeners;

use App\Notifications\Forms\FailedWebhookNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;

class FailedWebhookListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(WebhookCallFailedEvent $event)
    {
        // Notify form owner
        if ($event->meta['type'] == 'form_submission') {
            $event->meta['form']->creator->notify(new FailedWebhookNotification($event));
            \Log::error('Failed form submission webhook', [
                'webhook_url' => $event->webhookUrl,
                'exception' => $event->errorType,
                'message' => $event->errorMessage,
                'form_id' => $event->meta['form']->id
            ]);
            return;
        }

        \Log::error('Failed webhook', [
            'webhook_url' => $event->webhookUrl,
            'exception' => $event->errorType,
            'message' => $event->errorMessage
        ]);
    }
}
