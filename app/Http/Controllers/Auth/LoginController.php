<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function logout()
    {
        $token = Auth::user()->token();

        if($token->exists) {
            $token->revoke();
        }

        return response([]);
    }
}
