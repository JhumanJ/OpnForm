<?php

namespace App\Service\Forms\Webhooks;

use App\Models\Forms\Form;

class ZapierHandler extends AbstractWebhookHandler
{
    public function __construct(protected Form $form, protected array $data, protected string $webhookUrl)
    {
    }

    protected function getProviderName(): ?string
    {
        return 'zapier';
    }

    protected function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl());
    }
}
