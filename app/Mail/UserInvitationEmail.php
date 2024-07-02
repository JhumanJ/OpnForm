<?php

namespace App\Mail;

use App\Models\UserInvite;
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
    public function __construct(public UserInvite $invite)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $workspaceName = $this->invite->workspace->name;
        return $this
            ->markdown('mail.user.invitation', [
                'workspaceName' => $workspaceName,
                'inviteLink' => $this->invite->getLink(),
            ])->subject('You are invited to join ' . $workspaceName . ' on OpnForm');
    }
}
