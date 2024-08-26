<?php

namespace App\Notifications\Subscription;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FailedPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        return (new MailMessage())
            ->subject('Your Payment Failed')
            ->greeting(__('We tried to charge your card for your OpenForm subscription but the payment but did not work.'))
            ->line(__('Please go to OpenForm, click on your name on the top right corner, and click on "Billing".
            You will then be able to update your card details. To avoid any service disruption, you can reply to this email whenever
            you updated your card details, and we\'ll manually attempt to charge your card.'))
            ->action(__('Go to OpenForm'), front_url('/'));
    }
}
