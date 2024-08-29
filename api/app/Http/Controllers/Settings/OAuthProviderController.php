<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\OAuthProviderService;
use App\Models\OAuthProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        cache()->put("oauth-intention:{$userId}", $request->input('intention'), 60 * 5);

        // Connecting an account for integrations purposes
        // Adding full scopes to the driver
        return response()->json([
            'url' => $service->getDriver()->fullScopes()->getRedirectUrl(),
        ]);
    }

    public function handleRedirect(OAuthProviderService $service)
    {
        $driverUser = $service->getDriver()->getUser();

        $provider = OAuthProvider::query()
            ->updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'provider' => $service,
                    'provider_user_id' => $driverUser->getId(),
                ],
                [
                    'access_token' => $driverUser->token,
                    'refresh_token' => $driverUser->refreshToken,
                    'name' => $driverUser->getName(),
                    'email' => $driverUser->getEmail(),
                    'scopes' => $driverUser->approvedScopes
                ]
            );

        return OAuthProviderResource::make($provider);
    }

    public function destroy(OAuthProvider $provider)
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        return response()->json();
    }
}
