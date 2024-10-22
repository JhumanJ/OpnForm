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
use Symfony\Component\Mime\Email;

class FormEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public FormSubmitted $event;
    public string $mailer;
    private array $formattedData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FormSubmitted $event, private $integrationData, string $mailer)
    {
        $this->event = $event;
        $this->mailer = $mailer;
        $this->formattedData = $this->formatSubmissionData();
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
            ->from($this->getFromEmail(), $this->getSenderName())
            ->subject($this->getSubject())
            ->withSymfonyMessage(function (Email $message) {
                $this->addCustomHeaders($message);
            })
            ->markdown('mail.form.email-notification', $this->getMailData());
    }

    private function formatSubmissionData(): array
    {
        $formatter = (new FormSubmissionFormatter($this->event->form, $this->event->data))
            ->createLinks()
            ->outputStringsOnly()
            ->useSignedUrlForFiles();

        if ($this->integrationData->include_hidden_fields_submission_data ?? false) {
            $formatter->showHiddenFields();
        }

        return $formatter->getFieldsWithValue();
    }

    private function getFromEmail(): string
    {
        if (
            config('app.self_hosted')
            && isset($this->integrationData->sender_email)
            && $this->validateEmail($this->integrationData->sender_email)
        ) {
            return $this->integrationData->sender_email;
        }

        return config('mail.from.address');
    }

    private function getSenderName(): string
    {
        return $this->integrationData->sender_name ?? config('app.name');
    }

    private function getReplyToEmail($default): string
    {
        $replyTo = $this->integrationData->reply_to ?? null;

        if ($replyTo) {
            $parsedReplyTo = $this->parseReplyTo($replyTo);
            if ($parsedReplyTo && $this->validateEmail($parsedReplyTo)) {
                return $parsedReplyTo;
            }
        }

        return $this->getRespondentEmail() ?? $default;
    }

    private function parseReplyTo(string $replyTo): ?string
    {
        $parser = new MentionParser($replyTo, $this->formattedData);
        return $parser->parse();
    }

    private function getSubject(): string
    {
        $defaultSubject = 'New form submission';
        $parser = new MentionParser($this->integrationData->subject ?? $defaultSubject, $this->formattedData);
        return $parser->parse();
    }

    private function addCustomHeaders(Email $message): void
    {
        $formId = $this->event->form->id;
        $submissionId = $this->event->data['submission_id'] ?? 'unknown';
        $domain = Str::after(config('app.url'), '://');

        $uniquePart = substr(md5($formId . $submissionId), 0, 8);
        $messageId = "form-{$formId}-{$uniquePart}@{$domain}";
        $references = "form-{$formId}@{$domain}";

        $message->getHeaders()->remove('Message-ID');
        $message->getHeaders()->addIdHeader('Message-ID', $messageId);
        $message->getHeaders()->addTextHeader('References', $references);
    }

    private function getMailData(): array
    {
        return [
            'emailContent' => $this->getEmailContent(),
            'fields' => $this->formattedData,
            'form' => $this->event->form,
            'integrationData' => $this->integrationData,
            'noBranding' => $this->event->form->no_branding,
            'submission_id' => $this->getEncodedSubmissionId(),
        ];
    }

    private function getEmailContent(): string
    {
        $parser = new MentionParser($this->integrationData->email_content ?? '', $this->formattedData);
        return $parser->parse();
    }

    private function getEncodedSubmissionId(): ?string
    {
        $submissionId = $this->event->data['submission_id'] ?? null;
        return $submissionId ? Hashids::encode($submissionId) : null;
    }

    private function getRespondentEmail(): ?string
    {
        $emailFields = ['email', 'e-mail', 'mail'];

        foreach ($this->formattedData as $field => $value) {
            if (in_array(strtolower($field), $emailFields) && $this->validateEmail($value)) {
                return $value;
            }
        }

        // If no email field found, search for any field containing a valid email
        foreach ($this->formattedData as $value) {
            if ($this->validateEmail($value)) {
                return $value;
            }
        }

        return null;
    }

    public static function validateEmail($email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
