<?php

namespace App\Service\Forms\Integrations\Events;

use App\Integrations\Google\Google;
use App\Models\Integration\FormIntegration;

class GoogleSheetsIntegrationCreated extends AbstractIntegrationCreated
{
    protected Google $client;

    public function __construct(
        protected FormIntegration $formIntegration
    ) {
        parent::__construct($formIntegration);

        $this->client = new Google($formIntegration->provider);
    }

    public function handle(): void
    {
        $spreadsheet = $this->client->sheets()
            ->create($this->formIntegration->form->title);

        $this->formIntegration->update([
            'data' => [
                'spreadsheet_id' => $spreadsheet->spreadsheetId,
            ],
        ]);
    }
}
