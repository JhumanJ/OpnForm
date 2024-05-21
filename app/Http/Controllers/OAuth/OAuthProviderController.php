<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\OAuthProviderService;
use App\Models\OAuthProvider;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

class OAuthProviderController extends Controller
{
    // TODO user_id
    public function index()
    {
        $providers = Workspace::first()->providers()->get();

        return OAuthProviderResource::collection($providers);
    }

    // TODO authorization
    public function connect(OAuthProviderService $service)
    {
        return response()->json([
            'url' => $service->getDriver()->getRedirectUrl(),
        ]);
    }

    public function handleRedirect(OAuthProviderService $service)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $driverUser = $service->getDriver()->getUser();

        $provider = OAuthProvider::query()
            ->updateOrCreate(
                [
                    'workspace_id' => $user->workspaces()->first()->id,
                    'provider' => 'google',
                    'provider_user_id' => $driverUser->getId(),
                ],
                [
                    'access_token' => $driverUser->token,
                    'refresh_token' => $driverUser->refreshToken,
                    'name' => $driverUser->getName(),
                    'email' => $driverUser->getEmail(),
                ]
            );

        return OAuthProviderResource::make($provider);
    }

    // TODO authorization
    public function destroy(OAuthProvider $provider)
    {
        $provider->delete();

        return response()->json([]);
    }
}
