<?php

namespace App\Mail\Forms;

use App\Events\Forms\FormSubmitted;
use App\Mail\OpenFormMail;
use App\Open\MentionParser;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class SubmissionConfirmationMail extends OpenFormMail implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private $formattedData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private FormSubmitted $event, private $integrationData)
    {
        $formatter = (new FormSubmissionFormatter($event->form, $event->data))
            ->createLinks()
            ->outputStringsOnly()
            ->useSignedUrlForFiles();

        $this->formattedData = $formatter->getFieldsWithValue();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $form = $this->event->form;

       
        return $this
            ->replyTo($this->getReplyToEmail($form->creator->email))
            ->from($this->getFromEmail(), $this->integrationData->notification_sender)
            ->subject($this->getSubject())
            ->markdown('mail.form.confirmation-submission-notification', [
                'fields' => $this->formattedData,
                'form' => $form,
                'integrationData' => $this->integrationData,
                'noBranding' => $form->no_branding,
                'submission_id' => (isset($this->event->data['submission_id']) && $this->event->data['submission_id']) ? Hashids::encode($this->event->data['submission_id']) : null,
            ]);
    }

    private function getFromEmail()
    {
        if (config('app.self_hosted')) {
            return config('mail.from.address');
        }

        $originalFromAddress = Str::of(config('mail.from.address'))->explode('@');

        return $originalFromAddress->first() . '+' . time() . '@' . $originalFromAddress->last();
    }

    private function getReplyToEmail($default)
    {
        $replyTo = $this->integrationData->confirmation_reply_to ?? null;

        if ($replyTo) {
            $parser = new MentionParser($replyTo, $this->formattedData);
            $parsedReplyTo = $parser->parse();
            if ($parsedReplyTo && filter_var($parsedReplyTo, FILTER_VALIDATE_EMAIL)) {
                return $parsedReplyTo;
            }
        }
        return $default;
    }

    private function getSubject()
    {
        $parser = new MentionParser($this->integrationData->notification_subject, $this->formattedData);
        return $parser->parse();
    }

    private function getNotificationBody()
    {
        $parser = new MentionParser($this->integrationData->notification_body, $this->formattedData);
        return $parser->parse();
    }
}
