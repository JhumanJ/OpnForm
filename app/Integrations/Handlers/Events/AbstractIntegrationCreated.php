<?php

namespace App\Integrations\Handlers\Events;

use App\Models\Integration\FormIntegration;

class AbstractIntegrationCreated
{
    public function __construct(
        protected FormIntegration $formIntegration
    ) {
    }

    public function handle(): void
    {
        //
    }
}
