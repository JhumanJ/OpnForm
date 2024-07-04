<?php

namespace App\Service;

use App\Models\User;

class UserHelper
{
    public function __construct(public User $user)
    {

    }

    /**
     * Function to get to total number of active members in each of this user's workspaces
     */
    public function getActiveMembersCount(): ?int
    {
        $count = 1;
        foreach ($this->user->workspaces as $workspace) {
            $count += $workspace->users()->where('users.id', '!=', $this->user->id)->count();
        }
        return $count;
    }

}
