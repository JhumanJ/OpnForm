<?php

namespace App\Integrations\OAuth\Drivers\Contracts;

use Laravel\Socialite\Contracts\User;

interface OAuthDriver
{
    public function getRedirectUrl(): string;
    public function getUser(): User;
}
