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
        $count = 0;
        foreach ($this->user->workspaces as $workspace) {
            $count += $workspace->users()->count();
        }
        return $count;
    }

}
