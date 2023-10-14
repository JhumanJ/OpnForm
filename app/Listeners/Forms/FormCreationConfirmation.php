<?php

namespace App\Listeners\Forms;

use App\Events\Models\FormCreated;
use App\Mail\Forms\FormCreationConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class FormCreationConfirmation implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FormCreated $event)
    {
        Mail::to($event->form->creator)->send(new FormCreationConfirmationMail($event->form));
    }
}
