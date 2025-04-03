<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\OAuthProviderService;
use App\Models\OAuthProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OAuthProviderController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $providers = $user->oauthProviders()->get();

        return OAuthProviderResource::collection($providers);
    }

    public function connect(Request $request, OAuthProviderService $service)
    {
        $userId = Auth::id();
        $context = [
            'intention' => $request->input('intention'),
            'autoClose' => $request->boolean('autoClose', false),
        ];
        Cache::put("oauth-context:{$userId}", $context, now()->addMinutes(5));

        // Connecting an account for integrations purposes
        // Adding full scopes to the driver
        return response()->json([
            'url' => $service->getDriver()->fullScopes()->getRedirectUrl(),
        ]);
    }

    public function handleRedirect(Request $request, OAuthProviderService $service)
    {
        $userId = Auth::id();
        $context = Cache::pull("oauth-context:{$userId}", [
            'intention' => null,
            'autoClose' => false
        ]);
        $autoClose = $context['autoClose'];
        $intention = $context['intention'];

        try {
            $driverUser = $service->getDriver()->getUser();

            $provider = OAuthProvider::query()
                ->updateOrCreate(
                    [
                        'user_id' => $userId,
                        'provider' => $service,
                        'provider_user_id' => $driverUser->getId(),
                    ],
                    [
                        'access_token' => $driverUser->token,
                        'refresh_token' => $driverUser->refreshToken,
                        'name' => $driverUser->getName() ?? $driverUser->getNickname(),
                        'email' => $driverUser->getEmail(),
                        'scopes' => $driverUser->approvedScopes ?? [],
                    ]
                );

            return response()->json([
                'provider' => OAuthProviderResource::make($provider),
                'autoClose' => $autoClose,
                'intention' => $intention,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to connect the account. Please try again.'], 400);
        }
    }

    public function destroy(OAuthProvider $provider)
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        return response()->json();
    }
}
