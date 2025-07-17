<?php

namespace App\Service;

use App\Http\Controllers\Admin\AdminController;
use App\Mail\UserBlockedEmail;
use App\Mail\UserUnblockedEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserActionService
{
    public function block(User $user, string $reason, int $moderatorId): User
    {
        $user->blockUser($reason, $moderatorId);

        $user->forms()->update(['visibility' => 'draft']);

        AdminController::log('User blocked', [
            'user_id' => $user->id,
            'reason' => $reason,
            'moderator_id' => $moderatorId,
        ]);

        Mail::to($user)->send(new UserBlockedEmail($user, $reason));

        return $user->fresh();
    }

    public function unblock(User $user, string $reason, int $moderatorId): User
    {
        $user->unblockUser($reason, $moderatorId);

        AdminController::log('User unblocked', [
            'user_id' => $user->id,
            'reason' => $reason,
            'moderator_id' => $moderatorId,
        ]);

        Mail::to($user)->send(new UserUnblockedEmail($user, $reason));

        return $user->fresh();
    }
}
