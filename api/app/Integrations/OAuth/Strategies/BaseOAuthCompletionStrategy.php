<?php

namespace App\Integrations\OAuth\Strategies;

use App\Integrations\OAuth\OAuthProviderService;
use App\Models\OAuthProvider;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

abstract class BaseOAuthCompletionStrategy
{
    /**
     * Creates or updates an OAuthProvider record and associates it with a user.
     * This is the shared logic used by all OAuth completion flows.
     */
    protected function upsertProvider(User $user, OAuthProviderService $provider, SocialiteUser $socialiteUser): OAuthProvider
    {
        return OAuthProvider::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_user_id' => $socialiteUser->getId(),
            ],
            [
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
                'email' => $socialiteUser->getEmail(),
                'scopes' => $socialiteUser->approvedScopes ?? [],
            ]
        );
    }
}
