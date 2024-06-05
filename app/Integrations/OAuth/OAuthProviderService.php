<?php

namespace App\Integrations\OAuth;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use App\Integrations\OAuth\Drivers\OAuthGoogleDriver;

enum OAuthProviderService: string
{
    case Google = 'google';

    public function getDriver(): OAuthDriver
    {
        return match($this) {
            self::Google =>  new OAuthGoogleDriver()
        };
    }
}
