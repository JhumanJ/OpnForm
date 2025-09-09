<?php

namespace App\Service\OAuth;

use App\Models\UserInvite;
use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;

class OAuthInviteService
{
    /**
     * Validate invite token and return the invite
     */
    public function validateInviteToken(string $inviteToken): UserInvite
    {
        $userInvite = UserInvite::where('token', $inviteToken)
            ->where('status', UserInvite::PENDING_STATUS)
            ->first();

        if (!$userInvite) {
            abort(400, 'Invalid invite token');
        }

        if ($userInvite->hasExpired()) {
            abort(400, 'Invite token has expired');
        }

        return $userInvite;
    }

    /**
     * Get invited email from token
     */
    public function getInvitedEmail(string $inviteToken): string
    {
        return $this->validateInviteToken($inviteToken)->email;
    }

    /**
     * Configure driver with email restrictions (generic approach)
     */
    public function configureDriverEmailRestrictions(
        OAuthDriver $driver,
        ?string $invitedEmail = null
    ): void {
        if (!$invitedEmail || !$driver->supportsEmailRestrictions()) {
            return;
        }

        $driver->setEmailRestrictions([$invitedEmail]);
    }

    /**
     * Validate email matches invited email for OAuth authentication
     */
    public function validateEmailRestrictions(string $email, ?string $invitedEmail = null): void
    {
        if ($invitedEmail && strtolower($email) !== strtolower($invitedEmail)) {
            abort(400, 'You must login with the invited email address: ' . $invitedEmail);
        }
    }
}
