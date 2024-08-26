<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     */
    public function current(Request $request)
    {
        return new UserResource($request->user());
    }

    public function deleteAccount()
    {
        $this->middleware('auth');
        if (Auth::user()->admin) {
            return $this->error([
                'message' => 'Cannot delete an admin. Stay with us 🙏',
            ]);
        }
        Auth::user()->delete();

        return $this->success([
            'message' => 'User deleted.',
        ]);
    }
}
