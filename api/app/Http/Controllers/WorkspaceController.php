<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspace\CustomDomainRequest;
use App\Http\Requests\Workspace\EmailSettingsRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Workspace::class);

        return WorkspaceResource::collection(Auth::user()->workspaces);
    }

    public function saveCustomDomain(CustomDomainRequest $request)
    {
        if (!$request->workspace->is_pro) {
            return $this->error([
                'message' => 'A Pro plan is required to use this feature.',
            ], 403);
        }

        $request->workspace->custom_domains = $request->customDomains;
        $request->workspace->save();

        return new WorkspaceResource($request->workspace);
    }

    public function saveEmailSettings(EmailSettingsRequest $request)
    {
        if (!$request->workspace->is_pro) {
            return $this->error([
                'message' => 'A Pro plan is required to use this feature.',
            ], 403);
        }

        $request->workspace->update(['settings' => array_merge($request->workspace->settings, ['email_settings' => $request->validated()])]);

        return new WorkspaceResource($request->workspace);
    }

    public function delete($id)
    {
        $workspace = Workspace::findOrFail($id);
        $this->authorize('delete', $workspace);

        $id = $workspace->id;
        $workspace->delete();

        return $this->success([
            'message' => 'Workspace deleted.',
            'workspace_id' => $id,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'name' => 'required',
        ]);

        // Create workspace
        $workspace = Workspace::create([
            'name' => $request->name,
            'icon' => ($request->emoji) ? $request->emoji : '',
        ]);

        // Add relation with user
        $user->workspaces()->sync([
            $workspace->id => [
                'role' => 'admin',
            ],
        ], false);

        return $this->success([
            'message' => 'Workspace created.',
            'workspace_id' => $workspace->id,
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }
}
