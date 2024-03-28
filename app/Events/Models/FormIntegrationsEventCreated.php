<?php

namespace App\Events\Models;

use App\Models\Integration\FormIntegrationsEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormIntegrationsEventCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public FormIntegrationsEvent $formIntegrationsEvent)
    {
        //
    }
}
