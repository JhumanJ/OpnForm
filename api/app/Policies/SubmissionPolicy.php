<?php

namespace App\Policies;

use App\Models\Forms\FormSubmission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubmissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any submissions.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific submission.
     */
    public function view(User $user, FormSubmission $submission): bool
    {
        // Sanctum token logic
        if ($token = $user->currentAccessToken()) {
            return $token->can('submissions:read') && $user->ownsForm($submission->form);
        }

        // Fallback to JWT / session logic
        return $user->ownsForm($submission->form);
    }

    /**
     * Determine whether the user can update a submission.
     */
    public function update(User $user, FormSubmission $submission): bool
    {
        // Basic ownership & workspace role check via FormPolicy pattern
        $ownsForm = $user->ownsForm($submission->form);
        $isWritable = !$submission->form->workspace->isReadonlyUser($user);

        if (!($ownsForm && $isWritable)) {
            return false;
        }

        if ($token = $user->currentAccessToken()) {
            return $token->can('submissions:write');
        }

        return true;
    }

    /**
     * Alias delete to update permission (write ability required)
     */
    public function delete(User $user, FormSubmission $submission): bool
    {
        return $this->update($user, $submission);
    }
}