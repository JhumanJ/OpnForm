<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspace\CustomDomainRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Models\User;
use App\Service\WorkspaceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInvitationEmail;

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

    public function listUsers(Request $request, $workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $this->authorize('view', $workspace);

        return (new WorkspaceHelper($workspace))->getAllUsers();
    }

    public function addUser(Request $request, $workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $this->authorize('workspaceAdmin', $workspace);

        $this->validate($request, [
            'email' => 'required|email',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Mail::to($request->email)->send(new UserInvitationEmail($workspace->name));
            return $this->success([
                'message' => 'Registration invitation email sent to user.'
            ]);
        }

        $workspace->users()->sync([
            $user->id => [
                'role' => $request->role,
            ],
        ], false);

        return $this->success([
            'message' => 'User has been successfully added to workspace.'
        ]);
    }

    public function updateUserRole(Request $request, $workspaceId, $userId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = User::findOrFail($userId);
        $this->authorize('workspaceAdmin', $workspace);

        $this->validate($request, [
            'role' => 'required|in:admin,user',
        ]);

        $workspace->users()->sync([
            $user->id => [
                'role' => $request->role,
            ],
        ], false);

        return $this->success([
            'message' => 'User role changed successfully.'
        ]);
    }

    public function removeUser(Request $request, $workspaceId, $userId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $this->authorize('workspaceAdmin', $workspace);

        $workspace->users()->detach($userId);

        return $this->success([
            'message' => 'User removed from workspace successfully.'
        ]);
    }

    public function leaveWorkspace(Request $request, $workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $this->authorize('view', $workspace);

        $workspace->users()->detach($request->user()->id);

        return $this->success([
            'message' => 'You have left the workspace successfully.'
        ]);
    }

    public function saveCustomDomain(CustomDomainRequest $request)
    {
        $request->workspace->custom_domains = $request->customDomains;
        $request->workspace->save();

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
