<?php

namespace App\Notifications\Forms;

use App\Events\Forms\FormSubmitted;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FormSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public FormSubmitted $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FormSubmitted $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $formatter = (new FormSubmissionFormatter($this->event->form, $this->event->data))
            ->showHiddenFields()
            ->createLinks()
            ->outputStringsOnly();

        return (new MailMessage)
            ->replyTo($notifiable->routes['mail'])
            ->from($this->getFromEmail(), config('app.name'))
            ->subject('New form submission for "'.$this->event->form->title.'"')
            ->markdown('mail.form.submission-notification', [
                'fields' => $formatter->getFieldsWithValue(),
                'form' => $this->event->form,
            ]);
    }

    private function getFromEmail()
    {
        $originalFromAddress = Str::of(config('mail.from.address'))->explode('@');
        return $originalFromAddress->first(). '+' . time() . '@' . $originalFromAddress->last();
    }

}
