<?php

namespace App\Policies;

use App\Models\Forms\FormSubmission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormSubmissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the form submission.
     */
    public function update(User $user, FormSubmission $submission)
    {
        $form = $submission->form;

        // Check if user owns the form (through workspace ownership)
        return $user->ownsForm($form);
    }
}
