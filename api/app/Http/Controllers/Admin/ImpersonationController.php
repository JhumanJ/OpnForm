<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ImpersonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function impersonate($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return $this->error([
                'message' => 'User not found.',
            ]);
        } elseif ($user->admin) {
            return $this->error([
                'message' => 'You cannot impersonate an admin.',
            ]);
        }

        AdminController::log('Impersonation started', [
            'from_id' => auth()->id(),
            'from_email' => auth()->user()->email,
            'target_id' => $user->id,
            'target_email' => $user->email,
            'target_is_blocked' => $user->is_blocked,
        ]);

        // Enhanced JWT claims: admins get admin_impersonating, moderators get impersonating
        $claims = auth()->user()->admin ? [
            'admin_impersonating' => true,
            'impersonator_id' => auth()->id(),
        ] : [
            'impersonating' => true,
            'impersonator_id' => auth()->id(),
        ];

        $token = auth()->claims($claims)->login($user);

        return $this->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->getPayload()->get('exp') - time(),
        ]);
    }
}
