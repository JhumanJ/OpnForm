<?php

namespace App\Service\Forms\Integrations;

class WebhookIntegration extends AbstractIntegrationHandler
{
    public static function getValidationRules(): array
    {
        return [
            'webhook_url' => 'required|url'
        ];
    }

    protected function getWebhookUrl(): ?string
    {
        return $this->integrationData->webhook_url;
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl()) && parent::shouldRun();
    }
}
