<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Models\User;

class ImpersonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function impersonate($identifier)
    {
        $user = null;
        if (is_numeric($identifier)) {
            $user = User::find($identifier);
        } elseif (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::whereEmail($identifier)->first();
        } else {
            // Find by form slug
            $form = Form::whereSlug($identifier)->first();
            if ($form) {
                $user = $form->creator;
            }
        }

        if (! $user) {
            return $this->error([
                'message' => 'User not found.',
            ]);
        } elseif ($user->admin) {
            return $this->error([
                'message' => 'You cannot impersonate an admin.',
            ]);
        }

        \Log::warning('Impersonation started', [
            'from_id' => auth()->id(),
            'from_email' => auth()->user()->email,
            'target_id' => $user->id,
            'target_email' => $user->id,
        ]);

        $token = auth()->claims(auth()->user()->admin ? [] : [
            'impersonating' => true,
            'impersonator_id' => auth()->id(),
        ])->login($user);

        return $this->success([
            'token' => $token,
        ]);
    }
}
