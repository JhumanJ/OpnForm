<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitationEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;


    /**
     * Create a new message instance.
     *
     * @param string $workspaceName
     * @return void
     */
    public function __construct(public string $workspaceName, public string $inviteLink)
    {
        $this->workspaceName = $workspaceName;
        $this->inviteLink = $inviteLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('mail.user.invitation', [
                'workspaceName' => $this->workspaceName,
            ])->subject('You are invited to join ' . $this->workspaceName . ' on OpnForm');
    }
}
