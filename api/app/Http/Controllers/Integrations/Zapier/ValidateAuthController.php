<?php

namespace App\Http\Controllers\Integrations\Zapier;

use Illuminate\Support\Facades\Auth;

class ValidateAuthController
{
    public function __invoke()
    {
        $user = Auth::user();

        return [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
