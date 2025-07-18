<?php

namespace App\Service\OAuth;

use App\Integrations\OAuth\OAuthProviderService;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\HttpResponseException;

class OAuthUserService
{
    public function findOrCreateUser(array $userData, OAuthProviderService $providerService): User
    {
        $email = strtolower($userData['email']);
        $user = User::whereEmail($email)->first();

        if ($user) {
            // Check if user has this specific OAuth provider linked
            $hasOAuthProvider = $user->oauthProviders()
                ->where('provider', $providerService->getDatabaseProvider())
                ->where('provider_user_id', $userData['provider_user_id'])
                ->exists();

            if ($hasOAuthProvider) {
                // User has this specific OAuth provider linked - allow authentication
                return $user;
            }

            // User exists but doesn't have this OAuth provider linked and no password
            // This prevents account takeover via different OAuth providers with same email
            throw new HttpResponseException(
                response()->json([
                    'message' => 'An account with this email already exists. Please sign in with your original method or contact support.',
                    'error' => 'email_already_exists_different_provider'
                ], 409)
            );
        }

        // No existing user - create new account
        // Check if registration is allowed in self-hosted mode
        if (config('app.self_hosted') && app()->environment() !== 'testing') {
            abort(422, 'User registration is not allowed.');
        }

        // Get UTM data from context
        $context = Cache::get("oauth-context:auth:" . session()->getId(), []);
        $utmData = $context['utm_data'] ?? null;

        $user = User::create([
            'name' => $userData['name'],
            'email' => $email,
            'email_verified_at' => now(),
            'utm_data' => is_string($utmData) ? json_decode($utmData, true) : $utmData,
            'meta' => [
                'signup_provider' => $providerService->value,
                'signup_provider_user_id' => $userData['provider_user_id'],
                'registration_ip' => request()->ip()
            ],
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

        return $user;
    }
}
