<?php

namespace App\Service;

use App\Http\Controllers\Admin\AdminController;
use App\Mail\UserBlockedEmail;
use App\Mail\UserUnblockedEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserActionService
{
    public function block(User $user, string $reason, ?int $moderatorId): User
    {
        $user->blockUser($reason, $moderatorId);

        $user->forms()->update(['visibility' => 'draft']);

        AdminController::log('User blocked', [
            'user_id' => $user->id,
            'reason' => $reason,
            'moderator_id' => $moderatorId,
        ]);

        // Log to Slack
        if (app()->environment() !== 'testing') {
            Log::channel('slack_admin')->info('User blocked 🚫', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => $reason,
                'moderator_id' => $moderatorId,
                'actions' => [
                    'Admin Panel' => front_url('/admin?user_id=' . $user->id),
                ]
            ]);
        }

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

        // Log to Slack
        if (app()->environment() !== 'testing') {
            Log::channel('slack_admin')->info('User unblocked 🔓', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => $reason,
                'moderator_id' => $moderatorId,
                'actions' => [
                    'Admin Panel' => front_url('/admin?user_id=' . $user->id),
                ]
            ]);
        }

        Mail::to($user)->send(new UserUnblockedEmail($user, $reason));

        return $user->fresh();
    }
}
