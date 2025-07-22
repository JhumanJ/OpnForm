<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSaved;
use App\Jobs\Form\CheckFormForSpam;

class QueueSpamCheck
{
    public function handle(FormSaved $event): void
    {
        // We can add logic here to not queue for certain users if needed
        CheckFormForSpam::dispatch($event->form)->onQueue('default');
    }
}
