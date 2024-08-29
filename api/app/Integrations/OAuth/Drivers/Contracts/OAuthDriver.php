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
     * Set up all the scopes required by OpnForm for various integrations.
     * This method configures the necessary permissions for the current OAuth driver.
     */
    public function fullScopes(): self;
}
