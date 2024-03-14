<?php

namespace App\Service\Forms\Integrations;

class WebhookNotification extends AbstractIntegrationHandler
{
    protected function getWebhookUrl(): ?string
    {
        return $this->integrationData->webhook_url;
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl()) && $this->form->is_pro && parent::shouldRun();
    }
}
