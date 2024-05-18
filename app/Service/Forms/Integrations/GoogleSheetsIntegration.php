<?php

namespace App\Service\Forms\Integrations;

use App\Integrations\Google\Google;
use App\Service\Forms\FormSubmissionFormatter;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleSheetsIntegration extends AbstractIntegrationHandler
{
    public static function getValidationRules(): array
    {
        return [
            //
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

        $formatter = (new FormSubmissionFormatter($this->form, $this->submissionData))->outputStringsOnly();

        $row = array_map(
            fn ($field) => $field['value'],
            $formatter->getFieldsWithValue()
        );

        $client = new Google($this->formIntegration->provider);
        $client->sheets()
            ->addRow(
                $this->getSpreadsheetId(),
                $row
            );
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
