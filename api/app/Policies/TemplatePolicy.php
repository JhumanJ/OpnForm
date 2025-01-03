<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can perform write operations on the model.
     */
    private function canPerformWriteOperation(User $user, Template $template): bool
    {
        return $user->admin || $user->template_editor || $template->creator_id === $user->id;
    }

    public function update(User $user, Template $template)
    {
        return $this->canPerformWriteOperation($user, $template);
    }

    public function delete(User $user, Template $template)
    {
        return $this->canPerformWriteOperation($user, $template);
    }
}
