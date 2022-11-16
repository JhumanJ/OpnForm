<?php

namespace App\Mail\Forms;

use App\Events\Forms\FormSubmitted;
use App\Mail\OpenFormMail;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SubmissionConfirmationMail extends OpenFormMail implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private FormSubmitted $event)
    {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $form = $this->event->form;

        $formatter = (new FormSubmissionFormatter($form, $this->event->data))
            ->createLinks()
            ->outputStringsOnly();

        return $this
            ->replyTo($form->creator->email)
            ->from($this->getFromEmail(), $form->notification_sender)
            ->subject($form->notification_subject)
            ->markdown('mail.form.confirmation-submission-notification',[
                'fields' => $formatter->getFieldsWithValue(),
                'form' => $form,
                'noBranding' => $form->no_branding
            ]);
    }

    private function getFromEmail()
    {
        $originalFromAddress = Str::of(config('mail.from.address'))->explode('@');
        return $originalFromAddress->first(). '+' . time() . '@' . $originalFromAddress->last();
    }
}
