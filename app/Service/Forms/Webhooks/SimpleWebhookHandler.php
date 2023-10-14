<?php

namespace App\Service\Forms\Webhooks;

use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Support\Str;

class SimpleWebhookHandler extends AbstractWebhookHandler
{
    protected function getProviderName(): string
    {
        return 'webhook';
    }

    protected function getWebhookUrl(): ?string
    {
        return $this->form->webhook_url;
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl()) && $this->form->is_pro;
    }
}
