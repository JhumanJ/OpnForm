<?php

namespace App\Listeners\Forms;

use App\Events\Models\FormIntegrationCreated;
use App\Models\Integration\FormIntegration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FormIntegrationCreatedHandler implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FormIntegrationCreated $event)
    {
        $integration = FormIntegration::getIntegration($event->formIntegration->integration_id);

        if(!$integration) {
            return;
        }

        if(!isset($integration['file_name'])) {
            return;
        }

        $className = 'App\Integrations\Handlers\Events\\' . $integration['file_name'] . 'Created';

        if(!class_exists($className)) {
            return;
        }

        /** @var \App\Integrations\Handlers\Events\AbstractIntegrationCreated $eventHandler */
        $eventHandler = new $className($event->formIntegration);

        $eventHandler->handle();
    }
}
