<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSaved;
use App\Jobs\Form\CheckFormForSpam;
use Illuminate\Support\Facades\App;

class FormSpamCheckListener
{
    public function handle(FormSaved $event): void
    {
        if (App::environment('testing')) {
            return;
        }

        CheckFormForSpam::dispatch($event->form);
    }
}
