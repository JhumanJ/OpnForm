<?php

namespace App\Notifications\Forms;

use App\Models\Forms\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;

class FailedWebhookNotification extends Notification
{
    use Queueable;

    public Form $form;
    public string $provider;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected WebhookCallFailedEvent $event)
    {
        $this->form = $this->event->meta['form'];
        $this->provider = $this->event->meta['provider'];
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
        return (new MailMessage)
            ->subject("Notification issue with your NotionForm: '" . $this->form->title . "'")
            ->markdown('mail.form.webhook-error', [
                'provider' => $this->provider,
                'error' => $this->event->errorMessage,
                'form' => $this->form,
            ]);
    }
}
