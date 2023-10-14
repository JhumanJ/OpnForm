<?php

namespace App\Mail\Forms;

use App\Mail\OpenFormMail;
use App\Models\Forms\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormCreationConfirmationMail extends OpenFormMail implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Form $form)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('mail.form.created',[
            'form' => $this->form,
        ])->subject('Your form was created!');
    }
}
