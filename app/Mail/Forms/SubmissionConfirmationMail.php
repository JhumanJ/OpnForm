<?php

namespace App\Mail\Forms;

use App\Events\Forms\FormSubmitted;
use App\Mail\OpenFormMail;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class SubmissionConfirmationMail extends OpenFormMail implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private FormSubmitted $event)
    {
    }

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
            ->outputStringsOnly()
            ->useSignedUrlForFiles();

        return $this
            ->replyTo($this->getReplyToEmail($form->creator->email))
            ->from($this->getFromEmail(), $form->notification_sender)
            ->subject($form->notification_subject)
            ->markdown('mail.form.confirmation-submission-notification', [
                'fields' => $formatter->getFieldsWithValue(),
                'form' => $form,
                'noBranding' => $form->no_branding,
                'submission_id' => (isset($this->event->data['submission_id']) && $this->event->data['submission_id']) ? Hashids::encode($this->event->data['submission_id']) : null,
            ]);
    }

    private function getFromEmail()
    {
        $originalFromAddress = Str::of(config('mail.from.address'))->explode('@');

        return $originalFromAddress->first().'+'.time().'@'.$originalFromAddress->last();
    }

    private function getReplyToEmail($default)
    {
        $replyTo = Arr::get((array) $this->event->form->notification_settings, 'confirmation_reply_to', null);

        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            return $replyTo;
        }
        return $default;
    }
}
