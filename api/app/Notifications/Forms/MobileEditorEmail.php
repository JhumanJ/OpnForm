<?php

namespace App\Notifications\Forms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MobileEditorEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $slug;

    /**
     * Create a new notification instance.
     *
     * @param string $slug
     * @return void
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Continue editing your form on desktop')
            ->line('We noticed you\'re editing a form on smaller screen.')
            ->line('For the best form building experience, we recommend using a desktop computer.')
            ->line('Ready to create something amazing? Click below to continue editing. 💻')
            ->action(__('Continue Editing'), front_url('forms/' . $this->slug . '/edit'));
    }
}
