<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Models\User;
use Illuminate\Http\JsonResponse;

trait ManagesJWT
{
    /**
     * Send the response after the user was authenticated.
     */
    protected function sendLoginResponse(User $user): JsonResponse
    {
        $guard = auth('api');
        $token = $guard->login($user);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'new_user' => $user->new_user ?? false
        ]);
    }
}
