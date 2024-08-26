<?php

namespace App\Integrations\OAuth\Drivers\Contracts;

use Laravel\Socialite\Contracts\User;

interface OAuthDriver
{
    public function getRedirectUrl(): string;
    public function setRedirectUrl($url): self;
    public function getUser(): User;
    public function canCreateUser(): bool;
}
