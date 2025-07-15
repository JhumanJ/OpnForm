<?php

namespace App\Service\OAuth;

use App\Integrations\OAuth\OAuthProviderService;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Cache;

class OAuthUserService
{
    public function findOrCreateUser(array $userData, OAuthProviderService $providerService): User
    {
        $email = strtolower($userData['email']);
        $user = User::whereEmail($email)->first();

        if ($user && $user->has_registered) {
            // Existing user with password - they can use OAuth
            return $user;
        }

        if (!$user) {
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
                'meta' => ['signup_provider' => $providerService->value],
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

        return $user;
    }
}
