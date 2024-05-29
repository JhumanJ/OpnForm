<?php

namespace App\Integrations\Handlers\Events;

use App\Integrations\Google\Google;
use App\Models\Integration\FormIntegration;

class GoogleSheetsIntegrationCreated extends AbstractIntegrationCreated
{
    protected Google $client;

    public function __construct(
        protected FormIntegration $formIntegration
    ) {
        parent::__construct($formIntegration);

        $this->client = new Google($formIntegration);
    }

    public function handle(): void
    {
        $this->client->sheets()
            ->create($this->formIntegration->form);
    }
}
