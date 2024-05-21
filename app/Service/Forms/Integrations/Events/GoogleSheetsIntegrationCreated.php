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
        $manager = $this->client->sheets();

        $spreadsheet = $manager->create($this->formIntegration->form);

        $this->formIntegration->update([
            'data' => [
                'spreadsheet_id' => $spreadsheet->spreadsheetId,
                'url' => $spreadsheet->spreadsheetUrl,
            ],
        ]);
    }
}
