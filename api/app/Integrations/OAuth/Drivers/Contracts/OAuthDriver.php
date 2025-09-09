<?php

namespace App\Integrations\OAuth\Drivers\Contracts;

use Laravel\Socialite\Contracts\User;

interface OAuthDriver
{
    public function getRedirectUrl(): string;
    public function setRedirectUrl(string $url): self;
    public function setScopes(array $scopes): self;
    public function getUser(): User;
    public function canCreateUser(): bool;

    /**
     * Get the appropriate scopes for a given intent (auth or integration).
     */
    public function getScopesForIntent(string $intent): array;

    /**
     * Set email restrictions/hints for the OAuth flow
     * Default implementation should be no-op for drivers that don't support it
     */
    public function setEmailRestrictions(array $emails): self;

    /**
     * Check if this driver supports email restrictions
     */
    public function supportsEmailRestrictions(): bool;

    /**
     * Set additional OAuth parameters (generic method for customization)
     * This allows drivers to add custom parameters to the OAuth redirect
     */
    public function setAdditionalParameters(array $parameters): self;
}
