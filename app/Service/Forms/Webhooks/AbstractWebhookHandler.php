<?php

namespace App\Service\Forms\Webhooks;

use App\Models\Forms\Form;
use App\Service\Forms\FormSubmissionFormatter;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;
use Vinkla\Hashids\Facades\Hashids;

abstract class AbstractWebhookHandler
{
    public function __construct(protected Form $form, protected array $data)
    {
    }

    abstract protected function getProviderName(): ?string;

    abstract protected function getWebhookUrl(): ?string;

    /**
     * Default webhook payload. Can be changed in child classes.
     * @return array
     */
    protected function getWebhookData(): array
    {
        $formatter = (new FormSubmissionFormatter($this->form, $this->data))->showHiddenFields();

        $formattedData = [];
        foreach ($formatter->getFieldsWithValue() as $field) {
            $formattedData[$field['name']] = $field['value'];
        }

        $data = [
            'form_title' => $this->form->title,
            'form_slug' => $this->form->slug,
            'submission' => $formattedData,
        ];
        if ($this->form->is_pro && $this->form->editable_submissions) {
            $data['edit_link'] = $this->form->share_url . '?submission_id=' . Hashids::encode($this->data['submission_id']);
        }

        return $data;
    }

    abstract protected function shouldRun(): bool;

    public function handle()
    {
        if (!$this->shouldRun()) return;
        
        WebhookCall::create()
            // Add context on error, used to notify form owner
            ->meta([
                'type' => 'form_submission',
                'data' => $this->data,
                'form' => $this->form,
                'provider' => $this->getProviderName(),
            ])
            ->url($this->getWebhookUrl())
            ->doNotSign()
            ->payload($this->getWebhookData())
            ->dispatchSync();
    }
}
