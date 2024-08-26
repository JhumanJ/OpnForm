<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use App\Models\UserWorkspace;
use App\Service\UserHelper;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WorkspacePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Workspace $workspace)
    {
        return $user->ownsWorkspace($workspace);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Workspace $workspace)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Workspace $workspace)
    {
        return !$workspace->owners->where('id', $user->id)->isEmpty() && $user->workspaces()->count() > 1;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Workspace $workspace)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Workspace $workspace)
    {
        return false;
    }

    public function inviteUser(User $user, Workspace $workspace)
    {
        if (!$this->adminAction($user, $workspace)) {
            return Response::deny('You need to be an admin of this workspace to do this.');
        }

        // If self-hosted, allow
        if (!pricing_enabled()) {
            return Response::allow();
        }

        if (!$workspace->is_pro) {
            return Response::deny('You need a Pro subscription to invite a user.');
        }

        // In case of special license, check license limit
        $billingOwner = $workspace->billingOwners()->first();
        if ($license = $billingOwner->activeLicense()) {
            $userActiveMembers = (new UserHelper($billingOwner))->getActiveMembersCount();
            if ($userActiveMembers >= $license->max_users_limit_count) {
                return Response::deny('You have reached the maximum number of users allowed with your license.');
            }
        }

        return true;
    }

    /**
     * Determine whether the user is an admin in the workspace.
     *
     * @return mixed
     */
    public function adminAction(User $user, Workspace $workspace)
    {
        $userWorkspace = UserWorkspace::where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->first();
        return $userWorkspace && $userWorkspace->role === 'admin';
    }
}
