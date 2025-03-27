<?php

namespace App\Integrations\OAuth;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use App\Integrations\OAuth\Drivers\OAuthGoogleDriver;
use App\Integrations\OAuth\Drivers\OAuthTelegramDriver;

enum OAuthProviderService: string
{
    case Google = 'google';
    case Telegram = 'telegram';

    public function getDriver(): OAuthDriver
    {
        return match ($this) {
            self::Google =>  new OAuthGoogleDriver(),
            self::Telegram => new OAuthTelegramDriver(),
        };
    }
}
