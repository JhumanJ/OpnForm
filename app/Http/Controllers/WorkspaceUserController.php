<?php

namespace App\Http\Controllers;

use App\Jobs\Billing\WorkspaceUsersUpdated;
use App\Models\UserInvite;
use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\User;
use App\Service\WorkspaceHelper;

class WorkspaceUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
        $this->authorize('inviteUser', $workspace);

        $this->validate($request, [
            'email' => 'required|email',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->inviteUser($workspace, $request->email, $request->role);
        }

        if ($workspace->users->contains($user->id)) {
            return $this->success([
                'message' => 'User is already in workspace.'
            ]);
        }

        // User found - add user to workspace
        $workspace->users()->sync([
            $user->id => [
                'role' => $request->role,
            ],
        ], false);
        WorkspaceUsersUpdated::dispatch($workspace);

        return $this->success([
            'message' => 'User has been successfully added to workspace.'
        ]);
    }

    private function inviteUser(Workspace $workspace, string $email, string $role)
    {
        if (
            UserInvite::where('email', $email)
                ->where('workspace_id', $workspace->id)
                ->notExpired()
                ->pending()
                ->exists()) {
            return $this->success([
                'message' => 'User has already been invited.'
            ]);
        }

        // Send new invite
        UserInvite::inviteUser($email, $role, $workspace, now()->addDays(7));

        return $this->success([
            'message' => 'Registration invitation email sent to user.'
        ]);
    }

    public function updateUserRole(Request $request, $workspaceId, $userId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = User::findOrFail($userId);
        $this->authorize('adminAction', $workspace);

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
        $this->authorize('adminAction', $workspace);

        $workspace->users()->detach($userId);
        WorkspaceUsersUpdated::dispatch($workspace);

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
}
