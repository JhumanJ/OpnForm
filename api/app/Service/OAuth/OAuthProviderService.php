<?php

namespace App\Service\OAuth;

use App\Integrations\OAuth\OAuthProviderService as OAuthProviderServiceEnum;
use App\Models\OAuthProvider;
use App\Models\User;

class OAuthProviderService
{
    public function createOrUpdateProvider(User $user, OAuthProviderServiceEnum $providerService, array $userData): OAuthProvider
    {
        return OAuthProvider::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => $providerService->getDatabaseProvider(), // Use normalized provider name
                'provider_user_id' => $userData['provider_user_id'], // This saves Google ID!
            ],
            [
                'access_token' => $userData['access_token'],
                'refresh_token' => $userData['refresh_token'] ?? '',
                'name' => $userData['name'],
                'email' => $userData['email'],
                'scopes' => $userData['scopes'] ?? [],
            ]
        );
    }
}
