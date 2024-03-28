<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSubmitted;
use App\Models\Integration\FormIntegration;
use App\Service\Forms\Integrations\AbstractIntegrationHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $formIntegrations = FormIntegration::where([['form_id', $event->form->id], ['status', FormIntegration::STATUS_ACTIVE]])->get();
        foreach ($formIntegrations as $formIntegration) {
            ray($formIntegration, $formIntegration->integration_id);
            $this->getIntegrationHandler(
                $event,
                $formIntegration
            )->run();
        }

        /* $this->sendEmailNotifications($event);
        $this->sendWebhookNotification($event, WebhookHandlerProvider::SIMPLE_WEBHOOK_PROVIDER);
        $this->sendWebhookNotification($event, WebhookHandlerProvider::SLACK_PROVIDER);
        $this->sendWebhookNotification($event, WebhookHandlerProvider::DISCORD_PROVIDER);
        foreach ($event->form->zappierHooks as $hook) {
            $hook->triggerHook($event->data);
        }
        */
    }

    public static function getIntegrationHandler(FormSubmitted $event, FormIntegration $formIntegration): AbstractIntegrationHandler
    {
        $integration = FormIntegration::getIntegration($formIntegration->integration_id);
        if ($integration && isset($integration['file_name']) && class_exists('App\Service\Forms\Integrations\\' . $integration['file_name'])) {
            $className = 'App\Service\Forms\Integrations\\' . $integration['file_name'];
            return new $className($event, $formIntegration, $integration);
        }
        throw new \Exception('Unknown Integration!');
    }
}
