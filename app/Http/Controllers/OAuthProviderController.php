<?php

namespace App\Http\Controllers;

use App\Http\Resources\OAuthProviderResource;
use App\Models\Workspace;

class OAuthProviderController extends Controller
{
    public function index($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);

        $this->authorize('view', $workspace);

        $providers = $workspace->providers()->get();

        return OAuthProviderResource::collection($providers);
    }
}
