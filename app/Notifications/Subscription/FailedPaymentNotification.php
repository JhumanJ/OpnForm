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
        return (new MailMessage)
            ->subject('Thanh toán của bạn không thành công')
            ->greeting(__('Chúng tôi đã cố gắng tính phí vào thẻ cho đăng ký OpenForm của bạn nhưng việc thanh toán không thành công.'))
            ->line(__('Vui lòng truy cập OpenForm, nhấp vào tên của bạn ở góc trên bên phải và nhấp vào "Thanh toán".
            Sau đó, bạn sẽ có thể cập nhật thông tin thẻ của mình. Để tránh bất kỳ sự gián đoạn dịch vụ nào, bạn có thể trả lời email này bất cứ khi nào
            bạn đã cập nhật chi tiết thẻ của mình và chúng tôi sẽ cố gắng tính phí thẻ của bạn theo cách thủ công.'))
            ->action(__('Vào e-Form'), url('/'));
    }
}
