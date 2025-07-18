<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\OAuthProviderResource;
use App\Models\OAuthProvider;
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

    public function destroy(OAuthProvider $provider)
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        return response()->json();
    }
}
