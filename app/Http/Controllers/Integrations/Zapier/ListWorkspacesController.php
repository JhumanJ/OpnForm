<?php

namespace App\Http\Controllers\Integrations\Zapier;

use App\Http\Resources\Zapier\WorkspaceResource;
use Illuminate\Support\Facades\Auth;

class ListWorkspacesController
{
    public function __invoke()
    {
        return WorkspaceResource::collection(
            Auth::user()->workspaces()->get()
        );
    }
}
