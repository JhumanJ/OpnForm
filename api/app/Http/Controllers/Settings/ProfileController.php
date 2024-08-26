<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        return tap($user)->update([
            'name' => $request->name,
            'email' => strtolower($request->email),
        ]);
    }

    // For self-hosted mode, only admin can update their credentials
    public function updateAdminCredentials(Request $request)
    {
        $request->validate([
            'email' => 'required|email|not_in:admin@opnform.com',
            'password' => 'required|min:6|confirmed|not_in:password',
        ], [
            'email.not_in' => "Please provide email address other than 'admin@opnform.com'",
            'password.not_in' => "Please another password other than 'password'."
        ]);

        ray('in', $request->password);
        $user = $request->user();
        $user->update([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        ray($user);

        Cache::forget('initial_user_setup_complete');
        Cache::forget('max_user_id');

        $workspace = Workspace::create([
            'name' => 'My Workspace',
            'icon' => '🧪',
        ]);

        $user->workspaces()->sync([
            $workspace->id => [
                'role' => 'admin',
            ],
        ], false);

        return $this->success([
            'message' => 'Congratulations, your account credentials have been updated successfully.',
            'user' => $user,
        ]);
    }
}
