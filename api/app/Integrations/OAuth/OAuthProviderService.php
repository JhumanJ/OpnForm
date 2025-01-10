<?php

namespace App\Integrations\OAuth;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use App\Integrations\OAuth\Drivers\OAuthGoogleDriver;
use App\Integrations\OAuth\Drivers\OAuthStripeDriver;

enum OAuthProviderService: string
{
    case Google = 'google';
    case Stripe = 'stripe';

    public function getDriver(): OAuthDriver
    {
        return match ($this) {
            self::Google =>  new OAuthGoogleDriver(),
            self::Stripe =>  new OAuthStripeDriver()
        };
    }
}
