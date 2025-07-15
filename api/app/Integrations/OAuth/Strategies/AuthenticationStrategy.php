<?php

namespace App\Integrations\OAuth\Strategies;

use App\Http\Controllers\Auth\Traits\ManagesJWT;
use App\Integrations\OAuth\Contracts\OAuthCompletionStrategy;
use App\Integrations\OAuth\OAuthProviderService;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthenticationStrategy extends BaseOAuthCompletionStrategy implements OAuthCompletionStrategy
{
    use ManagesJWT;

    public function execute(OAuthProviderService $provider, SocialiteUser $socialiteUser): JsonResponse
    {
        $email = strtolower($socialiteUser->getEmail());
        $user = User::whereEmail($email)->first();

        if ($user && $user->has_registered) {
            return response()->json([
                "message" => "This email is already registered. Please sign in with your password."
            ], 422);
        }

        if (!$user) {
            // Check if registration is allowed in self-hosted mode
            if (config('app.self_hosted') && app()->environment() !== 'testing') {
                return response()->json([
                    "message" => "User registration is not allowed."
                ], 422);
            }

            $utmData = request()->utm_data;
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $email,
                'email_verified_at' => now(),
                'utm_data' => is_string($utmData) ? json_decode($utmData, true) : $utmData,
                'meta' => ['signup_provider' => $provider->value], // Track signup provider
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

        $this->upsertProvider($user, $provider, $socialiteUser);

        return $this->sendLoginResponse($user);
    }
}
