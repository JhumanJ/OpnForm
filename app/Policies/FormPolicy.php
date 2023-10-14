<?php

namespace App\Policies;

use App\Models\Forms\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forms\Form  $form
     * @return mixed
     */
    public function view(User $user, Form $form)
    {
        return $user->workspaces()->find($form->workspace_id) !== null;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forms\Form  $form
     * @return mixed
     */
    public function update(User $user, Form $form)
    {
        return $user->workspaces()->find($form->workspace_id) !== null;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forms\Form  $form
     * @return mixed
     */
    public function delete(User $user, Form $form)
    {
        return $user->workspaces()->find($form->workspace_id) !== null;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forms\Form  $form
     * @return mixed
     */
    public function restore(User $user, Form $form)
    {
        return $user->workspaces()->find($form->workspace_id) !== null;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Forms\Form  $form
     * @return mixed
     */
    public function forceDelete(User $user, Form $form)
    {
        return $user->workspaces()->find($form->workspace_id) !== null;
    }
}
