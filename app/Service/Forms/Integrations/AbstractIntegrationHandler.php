<?php

namespace App\Service\Forms\Integrations;

use App\Models\Integration\FormIntegration;
use App\Events\Forms\FormSubmitted;
use App\Models\Integration\FormIntegrationsEvent;
use App\Service\Forms\FormSubmissionFormatter;
use App\Service\Forms\FormLogicConditionChecker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Vinkla\Hashids\Facades\Hashids;

abstract class AbstractIntegrationHandler
{
    protected $form = null;
    protected $submissionData = null;
    protected $integrationData = null;

    public function __construct(
        protected FormSubmitted $event,
        protected FormIntegration $formIntegration,
        protected array $integration
    ) {
        $this->form = $event->form;
        $this->submissionData = $event->data;
        $this->integrationData = $formIntegration->data;
    }

    protected function getProviderName(): string
    {
        return $this->integration['name'] ?? '';
    }

    protected function logicConditionsMet(): bool
    {
        if (!$this->formIntegration->logic) {
            return true;
        }
        return FormLogicConditionChecker::conditionsMet(
            json_decode(json_encode($this->formIntegration->logic), true),
            $this->submissionData
        );
    }

    protected function shouldRun(): bool
    {
        return $this->logicConditionsMet();
    }

    protected function getWebhookUrl(): ?string
    {
        return '';
    }

    /**
     * Default webhook payload. Can be changed in child classes.
     */
    protected function getWebhookData(): array
    {
        $formatter = (new FormSubmissionFormatter($this->form, $this->submissionData))
            ->useSignedUrlForFiles()
            ->showHiddenFields();

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
            $data['edit_link'] = $this->form->share_url . '?submission_id=' . Hashids::encode(
                $this->submissionData['submission_id']
            );
        }

        return $data;
    }

    final public function run(): void
    {
        try {
            $this->handle();
            $this->formIntegration->events()->create([
                'status' => FormIntegrationsEvent::STATUS_SUCCESS,
            ]);
        } catch (\Exception $e) {
            $this->formIntegration->events()->create([
                'status' => FormIntegrationsEvent::STATUS_ERROR,
                'data' => $this->extractEventDataFromException($e),
            ]);
        }
    }

    /**
     * Default handle. Can be changed in child classes.
     */
    public function handle(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        Http::throw()->post($this->getWebhookUrl(), $this->getWebhookData());
    }

    abstract public static function getValidationRules(): array;

    public static function formatData(array $data): array
    {
        return $data;
    }

    public function extractEventDataFromException(\Exception $e): array
    {
        if ($e instanceof RequestException) {
            return [
                'message' => $e->getMessage(),
                'response' => $e->response->json(),
                'status' => $e->response->status(),
            ];
        }
        return [
            'message' => $e->getMessage()
        ];
    }
}
