<?php

namespace App\Integrations\OAuth\Strategies;

use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\OAuthProviderService;
use App\Models\OAuthProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WidgetStrategy extends BaseOAuthCompletionStrategy
{
    public function handleWidgetCallback(OAuthProviderService $service, Request $request): JsonResponse
    {
        $driver = $service->getDriver();

        if (!$driver instanceof \App\Integrations\OAuth\Drivers\Contracts\WidgetOAuthDriver) {
            abort(400, 'This provider does not support widget authentication');
        }

        $requestData = $request->all();

        if (!$driver->verifyWidgetData($requestData)) {
            abort(400, 'Invalid data signature');
        }

        $userData = $driver->getUserFromWidgetData($requestData);

        if (!Auth::check()) {
            return response()->json(['message' => 'Authentication required for widget connections.'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Create or update the OAuth provider record directly
        $oauthProvider = OAuthProvider::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => $service,
                'provider_user_id' => $userData['id'],
            ],
            [
                'access_token' => $userData['access_token'],
                'refresh_token' => $userData['refresh_token'] ?? '',
                'name' => $userData['name'],
                'email' => $userData['email'],
                'scopes' => $userData['scopes'] ?? [],
            ]
        );

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
