<?php

namespace App\Notifications\Forms;

use App\Events\Forms\FormSubmitted;
use App\Open\MentionParser;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class FormEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public FormSubmitted $event;
    public $mailer;
    private $formattedData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FormSubmitted $event, private $integrationData, string $mailer)
    {
        $this->event = $event;
        $this->mailer = $mailer;

        $formatter = (new FormSubmissionFormatter($event->form, $event->data))
            ->createLinks()
            ->outputStringsOnly()
            ->useSignedUrlForFiles();
        if ($this->integrationData->include_hidden_fields_submission_data ?? false) {
            $formatter->showHiddenFields();
        }
        $this->formattedData = $formatter->getFieldsWithValue();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->mailer($this->mailer)
            ->replyTo($this->getReplyToEmail($notifiable->routes['mail']))
            ->from($this->getFromEmail(), $this->integrationData->sender_name ?? config('app.name'))
            ->subject($this->getSubject())
            ->markdown('mail.form.email-notification', [
                'emailContent' => $this->getEmailContent(),
                'fields' => $this->formattedData,
                'form' => $this->event->form,
                'integrationData' => $this->integrationData,
                'noBranding' => $this->event->form->no_branding,
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
        $replyTo = $this->integrationData->reply_to ?? null;

        if ($replyTo) {
            $parser = new MentionParser($replyTo, $this->formattedData);
            $parsedReplyTo = $parser->parse();
            if ($parsedReplyTo && $this->validateEmail($parsedReplyTo)) {
                return $parsedReplyTo;
            }
        }

        return $this->getRespondentEmail() ?? $default;
    }

    private function getSubject()
    {
        $parser = new MentionParser($this->integrationData->subject, $this->formattedData);
        return $parser->parse();
    }

    private function getRespondentEmail()
    {
        // Make sure we only have one email field in the form
        $emailFields = collect($this->event->form->properties)->filter(function ($field) {
            $hidden = $field['hidden'] ?? false;

            return !$hidden && $field['type'] == 'email';
        });
        if ($emailFields->count() != 1) {
            return null;
        }

        if (isset($this->event->data[$emailFields->first()['id']])) {
            $email = $this->event->data[$emailFields->first()['id']];
            if ($this->validateEmail($email)) {
                return $email;
            }
        }

        return null;
    }

    private function getEmailContent()
    {
        $parser = new MentionParser($this->integrationData->email_content, $this->formattedData);
        return $parser->parse();
    }

    public static function validateEmail($email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
