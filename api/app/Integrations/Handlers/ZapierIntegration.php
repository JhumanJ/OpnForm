<?php

namespace App\Integrations\Handlers;

use App\Events\Forms\FormSubmitted;
use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use Exception;

class ZapierIntegration extends AbstractIntegrationHandler
{
    public function __construct(
        protected FormSubmitted $event,
        protected FormIntegration $formIntegration,
        protected array $integration
    ) {
        parent::__construct($event, $formIntegration, $integration);
    }

    public static function getValidationRules(?Form $form): array
    {
        return [];
    }

    public static function isOAuthRequired(): bool
    {
        return false;
    }

    protected function getWebhookUrl(): string
    {
        if (!isset($this->integrationData->hook_url)) {
            throw new Exception('The webhook URL is missing');
        }

        return $this->integrationData->hook_url;
    }

    protected function shouldRun(): bool
    {
        return parent::shouldRun() && $this->getWebhookUrl();
    }
}
