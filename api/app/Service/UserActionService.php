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
        $meta = $user->meta ?? [];
        $meta['block_reason'] = $reason;
        $meta['blocked_at'] = now();
        $meta['blocked_by'] = $moderatorId;
        $user->meta = $meta;
        $user->save();

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
        $meta = $user->meta ?? [];
        unset($meta['block_reason']);
        unset($meta['blocked_at']);
        unset($meta['blocked_by']);
        $meta['unblock_reason'] = $reason;
        $meta['unblocked_at'] = now();
        $meta['unblocked_by'] = $moderatorId;
        $user->meta = $meta;
        $user->save();

        AdminController::log('User unblocked', [
            'user_id' => $user->id,
            'reason' => $reason,
            'moderator_id' => $moderatorId,
        ]);

        Mail::to($user)->send(new UserUnblockedEmail($user, $reason));

        return $user->fresh();
    }
}
