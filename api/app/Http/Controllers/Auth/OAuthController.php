<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Integrations\OAuth\OAuthProviderService;
use App\Models\OAuthProvider;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class OAuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config([
            'services.github.redirect' => route('oauth.callback', 'github'),
        ]);
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(OAuthProviderService $provider)
    {
        return response()->json([
            'url' => $provider->getDriver()->setRedirectUrl(config('services.google.auth_redirect'))->getRedirectUrl()
        ]);
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string  $driver
     * @return \Illuminate\Http\Response
     */
    public function handleCallback(OAuthProviderService $provider)
    {
        try {
            $driverUser = $provider->getDriver()->setRedirectUrl(config('services.google.auth_redirect'))->getUser();
        } catch (\Exception $e) {
            return $this->error([
                "message" => "OAuth service failed to authenticate: " . $e->getMessage()
            ]);
        }

        $user = $this->findOrCreateUser($provider, $driverUser);

        if (!$user) {
            return $this->error([
                "message" => "User not found."
            ]);
        }

        if ($user->has_registered) {
            return $this->error([
                "message" => "This email is already registered. Please sign in with your password."
            ]);
        }

        $this->guard()->setToken(
            $token = $this->guard()->login($user)
        );

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->getPayload()->get('exp') - time(),
            'new_user' => $user->new_user
        ]);
    }

    /**
     * @p   aram  \Laravel\Socialite\Contracts\User  $socialiteUser
     * @return \App\Models\User | null
     */
    protected function findOrCreateUser($provider, $socialiteUser)
    {
        $oauthProvider = OAuthProvider::where('provider', $provider)
            ->where('provider_user_id', $socialiteUser->getId())
            ->first();

        if ($oauthProvider) {
            $oauthProvider->update([
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'scopes' => $socialiteUser->approvedScopes
            ]);

            return $oauthProvider->user;
        }


        if (!$provider->getDriver()->canCreateUser()) {
            return null;
        }

        $email = strtolower($socialiteUser->getEmail());
        $user = User::whereEmail($email)->first();

        if ($user) {
            $user->has_registered = true;
            return $user;
        }

        $utmData = request()->utm_data;
        $user = User::create([
            'name' => $socialiteUser->getName(),
            'email' => $email,
            'email_verified_at' => now(),
            'utm_data' => is_string($utmData) ? json_decode($utmData, true) : $utmData
        ]);

        // Create and sync workspace
        $workspace = Workspace::create([
            'name' => 'My Workspace',
            'icon' => '🧪',
        ]);

        $user->workspaces()->sync([
            $workspace->id => [
                'role' => User::ROLE_ADMIN,
            ],
        ], false);
        $user->new_user = true;

        OAuthProvider::create(
            [
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_user_id' => $socialiteUser->getId(),
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'scopes' => $socialiteUser->approvedScopes
            ]
        );
        return $user;
    }
}
