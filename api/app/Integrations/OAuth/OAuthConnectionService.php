<?php

namespace App\Integrations\OAuth;

use App\Integrations\OAuth\Contracts\OAuthCompletionStrategy;
use Illuminate\Http\JsonResponse;

class OAuthConnectionService
{
    public function getRedirectUrl(OAuthProviderService $provider, array $scopes = []): string
    {
        $driver = $provider->getDriver();

        if (!empty($scopes)) {
            $driver->setScopes($scopes);
        }

        return $driver->getRedirectUrl();
    }

    public function handleCallback(OAuthProviderService $provider, OAuthCompletionStrategy $strategy): JsonResponse
    {
        try {
            $driverUser = $provider->getDriver()->getUser();
            return $strategy->execute($provider, $driverUser);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to authenticate with the provider. Please try again.'], 400);
        }
    }
}
