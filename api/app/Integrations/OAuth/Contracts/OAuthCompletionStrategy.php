<?php

namespace App\Integrations\OAuth\Contracts;

use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use App\Integrations\OAuth\OAuthProviderService;

interface OAuthCompletionStrategy
{
    public function execute(OAuthProviderService $provider, SocialiteUser $socialiteUser): JsonResponse;
}
