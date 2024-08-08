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
    public function redirect(OAuthProviderService $service)
    {
        return response()->json([
            'url' => $service->getDriver()->setRedirectUrl(config('services.google.auth_redirect'))->getRedirectUrl()
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
            $user = $this->findOrCreateUser($provider, $driverUser);
            if(!$user) {
                return $this->error([
                    "message" => "User not found."
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
        } catch(\Exception $e) {
            return $this->error([
                "message" => "OAuth service failed to authenticate: ". $e->getMessage()
            ]);
        }
    }

    /**
     * @param  \Laravel\Socialite\Contracts\User  $socialiteUser
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
            ]);

            return $oauthProvider->user;
        }


        if ($provider->getDriver()->canCreateUser()) {
            $user = User::whereEmail($socialiteUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'email_verified_at' => now(),
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
            }
            $user->oauthProviders()->create([
                'provider' => $provider,
                'provider_user_id' => $socialiteUser->getId(),
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
            ]);
            return $user;
        } else {
            return null;
        }
    }

    /**
     * @param  string  $provider
     * @param  \Laravel\Socialite\Contracts\User  $sUser
     * @return \App\Models\User
     */
    protected function createUser($provider, $sUser)
    {

        $user = User::create([
            'name' => $sUser->getName(),
            'email' => $sUser->getEmail(),
            'email_verified_at' => now(),
        ]);

        $user->oauthProviders()->create([
            'provider' => $provider,
            'provider_user_id' => $sUser->getId(),
            'access_token' => $sUser->token,
            'refresh_token' => $sUser->refreshToken,
        ]);

        return $user;
    }
}
