<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserBlockedEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public User $user;
    public string $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your account has been blocked')
            ->markdown('mail.user.blocked');
    }
}
