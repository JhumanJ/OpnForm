<?php

namespace App\Integrations\OAuth\Strategies;

use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\Contracts\OAuthCompletionStrategy;
use App\Integrations\OAuth\OAuthProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class IntegrationStrategy extends BaseOAuthCompletionStrategy implements OAuthCompletionStrategy
{
    public function execute(OAuthProviderService $provider, SocialiteUser $socialiteUser): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Authentication required for integration connections.'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $oauthProvider = $this->upsertProvider($user, $provider, $socialiteUser);

        $context = Cache::pull("oauth-context:{$user->id}", [
            'intention' => null,
            'autoClose' => false
        ]);

        return response()->json([
            'provider' => OAuthProviderResource::make($oauthProvider),
            'autoClose' => $context['autoClose'],
            'intention' => $context['intention'],
        ]);
    }
}
