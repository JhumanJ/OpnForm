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
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->template_editor;
    }
}
