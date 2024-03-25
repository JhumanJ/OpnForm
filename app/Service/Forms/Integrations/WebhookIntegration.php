<?php

namespace App\Service\Forms\Integrations;

class WebhookIntegration extends AbstractIntegrationHandler
{
    protected function getWebhookUrl(): ?string
    {
        return $this->integrationData->webhook_url;
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl()) && parent::shouldRun();
    }
}
