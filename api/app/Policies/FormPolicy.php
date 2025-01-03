<?php

namespace App\Policies;

use App\Models\Forms\Form;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
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
    public function view(User $user, Form $form)
    {
        return $user->ownsForm($form);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user, Workspace $workspace)
    {
        return !$workspace->isReadonlyUser($user);
    }

    /**
     * Determine whether the user can perform write operations on the model.
     */
    private function canPerformWriteOperation(User $user, Form $form): bool
    {
        return $user->ownsForm($form) && !$form->workspace->isReadonlyUser($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Form $form)
    {
        return $this->canPerformWriteOperation($user, $form);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Form $form)
    {
        return $this->canPerformWriteOperation($user, $form);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Form $form)
    {
        return $this->canPerformWriteOperation($user, $form);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Form $form)
    {
        return $this->canPerformWriteOperation($user, $form);
    }
}
