<?php

namespace App\Integrations\Handlers;

use App\Events\Forms\FormSubmitted;
use App\Models\Integration\FormIntegration;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZapierIntegration extends AbstractIntegrationHandler
{
    public function __construct(
        protected FormSubmitted $event,
        protected FormIntegration $formIntegration,
        protected array $integration
    ) {
        parent::__construct($event, $formIntegration, $integration);
    }

    public static function getValidationRules(): array
    {
        return [];
    }

    public static function isOAuthRequired(): bool
    {
        return false;
    }

    public function handle(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        Log::debug('Triggering Zapier Webhook', [
            'hook_url' => $this->getWebhookUrl(),
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
        ]);

        Http::post(
            $this->getWebhookUrl(),
            $this->getWebhookData()
        );
    }

    protected function getWebhookUrl(): string
    {
        if(!isset($this->integrationData->hook_url)) {
            throw new Exception('The webhook URL is missing');
        }

        return $this->integrationData->hook_url;
    }

    protected function shouldRun(): bool
    {
        return parent::shouldRun() && $this->getWebhookUrl();
    }
}
