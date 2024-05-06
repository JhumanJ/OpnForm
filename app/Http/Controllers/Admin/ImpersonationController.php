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
            'target_email' => $user->id,
        ]);

        $token = auth()->claims(
            auth()->user()->admin ? [] : [
                'impersonating' => true,
                'impersonator_id' => auth()->id(),
            ]
        )->login($user);

        return $this->success([
            'token' => $token,
        ]);
    }
}
