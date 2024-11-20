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

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FormSubmitted $event, private $integrationData, string $mailer)
    {
        $this->event = $event;
        $this->mailer = $mailer;
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
            ->replyTo($this->getReplyToEmail($this->event->form->creator->email))
            ->from($this->getFromEmail(), $this->getSenderName())
            ->subject($this->getSubject())
            ->withSymfonyMessage(function (Email $message) {
                $this->addCustomHeaders($message);
            })
            ->markdown('mail.form.email-notification', $this->getMailData());
    }

    private function formatSubmissionData($createLinks = true): array
    {
        $formatter = (new FormSubmissionFormatter($this->event->form, $this->event->data))
            ->outputStringsOnly()
            ->useSignedUrlForFiles();

        if ($createLinks) {
            $formatter->createLinks();
        }
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
        return $default;
    }

    private function parseReplyTo(string $replyTo): ?string
    {
        $parser = new MentionParser($replyTo, $this->formatSubmissionData(false));
        return $parser->parseAsText();
    }

    private function getSubject(): string
    {
        $defaultSubject = 'New form submission';
        $parser = new MentionParser($this->integrationData->subject ?? $defaultSubject, $this->formatSubmissionData(false));
        return $parser->parseAsText();
    }

    private function addCustomHeaders(Email $message): void
    {
        $formId = $this->event->form->id;
        $submissionId = $this->event->data['submission_id'] ?? 'unknown';
        $domain = Str::after(config('app.url'), '://');
        $timestamp = time();

        // Create a unique Message-ID for each submission
        $messageId = "<form-{$formId}-submission-{$submissionId}-{$timestamp}@{$domain}>";

        // Create a References header that links to the form, but not to specific submissions
        $references = "<form-{$formId}@{$domain}>";

        // Add our custom Message-ID as X-Custom-Message-ID
        $message->getHeaders()->addTextHeader('X-Custom-Message-ID', $messageId);

        // Add References header
        $message->getHeaders()->addTextHeader('References', $references);

        // Add a unique Thread-Index to further prevent grouping
        $threadIndex = base64_encode(pack('H*', md5($formId . $submissionId . $timestamp)));
        $message->getHeaders()->addTextHeader('Thread-Index', $threadIndex);
    }

    private function getMailData(): array
    {
        return [
            'emailContent' => $this->getEmailContent(),
            'fields' => $this->formatSubmissionData(),
            'form' => $this->event->form,
            'integrationData' => $this->integrationData,
            'noBranding' => $this->event->form->no_branding,
            'submission_id' => $this->getEncodedSubmissionId(),
        ];
    }

    private function getEmailContent(): string
    {
        $parser = new MentionParser($this->integrationData->email_content ?? '', $this->formatSubmissionData());
        return $parser->parse();
    }

    private function getEncodedSubmissionId(): ?string
    {
        $submissionId = $this->event->data['submission_id'] ?? null;
        return $submissionId ? Hashids::encode($submissionId) : null;
    }

    public static function validateEmail($email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
