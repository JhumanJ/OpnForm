<?php

namespace App\Enterprise\Oidc\Policies;

use App\Enterprise\Oidc\Models\IdentityConnection;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\WorkspacePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class IdentityConnectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, ?\App\Models\Workspace $workspace = null): bool
    {
        if ($workspace === null) {
            // Global connections - only admins can view
            return $user->admin;
        }

        // Workspace-scoped connections - check if user is workspace admin
        return (new WorkspacePolicy())->adminAction($user, $workspace);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IdentityConnection $identityConnection): bool
    {
        if ($identityConnection->workspace_id === null) {
            // Global connection - only admins can view
            return $user->admin;
        }

        // Workspace-scoped connection - check if user is workspace admin
        return (new WorkspacePolicy())->adminAction($user, $identityConnection->workspace);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?\App\Models\Workspace $workspace = null): bool
    {
        if ($workspace === null) {
            // Global connection - only admins can create
            return $user->admin;
        }

        // Workspace-scoped connection - check if user is workspace admin
        return (new WorkspacePolicy())->adminAction($user, $workspace);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IdentityConnection $identityConnection): bool
    {
        if ($identityConnection->workspace_id === null) {
            // Global connection - only admins can update
            return $user->admin;
        }

        // Workspace-scoped connection - check if user is workspace admin
        return (new WorkspacePolicy())->adminAction($user, $identityConnection->workspace);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IdentityConnection $identityConnection): bool
    {
        if ($identityConnection->workspace_id === null) {
            // Global connection - only admins can delete
            return $user->admin;
        }

        // Workspace-scoped connection - check if user is workspace admin
        return (new WorkspacePolicy())->adminAction($user, $identityConnection->workspace);
    }
}
