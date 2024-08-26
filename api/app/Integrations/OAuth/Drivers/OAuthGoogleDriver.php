<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use Google\Service\Sheets;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class OAuthGoogleDriver implements OAuthDriver
{
    private ?string $redirectUrl = null;

    protected GoogleProvider $provider;

    public function __construct()
    {
        $this->provider = Socialite::driver('google');
    }

    public function getRedirectUrl(): string
    {
        return $this->provider
            ->scopes([Sheets::DRIVE_FILE])
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.google.redirect'))
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent select_account'
            ])
            ->redirect()
            ->getTargetUrl();
    }

    public function getUser(): User
    {
        return $this->provider
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.google.redirect'))
            ->user();
    }

    public function canCreateUser(): bool
    {
        return true;
    }

    public function setRedirectUrl($url): OAuthDriver
    {
        $this->redirectUrl = $url;
        return $this;
    }

}
