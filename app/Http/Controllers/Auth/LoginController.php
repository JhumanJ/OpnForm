<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function logout()
    {
        Auth::user()->token()->revoke();

        return response([]);
    }
}
