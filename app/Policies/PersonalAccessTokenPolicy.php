<?php

namespace App\Policies;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenPolicy
{
    public function delete(User $user, PersonalAccessToken $token)
    {
        return $token->tokenable()->is($user);
    }
}
