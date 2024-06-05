<?php

namespace App\Integrations\Handlers;

use App\Events\Forms\FormSubmitted;
use App\Integrations\Google\Google;
use App\Models\Integration\FormIntegration;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleSheetsIntegration extends AbstractIntegrationHandler
{
    protected Google $client;

    public function __construct(
        protected FormSubmitted $event,
        protected FormIntegration $formIntegration,
        protected array $integration
    ) {
        parent::__construct($event, $formIntegration, $integration);

        $this->client = new Google($formIntegration);
    }

    public static function getValidationRules(): array
    {
        return [

        ];
    }

    public static function isOAuthRequired(): bool
    {
        return true;
    }

    public static function getValidationAttributes(): array
    {
        return [
            'oauth_id' => 'Google Account',
        ];
    }

    public function handle(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        Log::debug('Creating Google Spreadsheet record', [
            'spreadsheet_id' => $this->getSpreadsheetId(),
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
        ]);

        $this->client->sheets()->submit($this->submissionData);
    }

    protected function getSpreadsheetId(): string
    {
        if(!isset($this->integrationData->spreadsheet_id)) {
            throw new Exception('The spreadsheed is not instantiated');
        }

        return $this->integrationData->spreadsheet_id;
    }

    protected function shouldRun(): bool
    {
        return parent::shouldRun() && $this->formIntegration->oauth_id && $this->getSpreadsheetId();
    }
}
