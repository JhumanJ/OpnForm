<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class OAuthTelegramDriver implements OAuthDriver
{
    private ?string $redirectUrl = null;
    private ?array $scopes = [];

    protected AbstractProvider $provider;

    public function __construct()
    {
        $this->provider = Socialite::driver('telegram');
    }

    public function getRedirectUrl(): string
    {
        $provider = $this->provider
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.telegram.redirect'))
            ->with([
                'bot_id' => config('services.telegram.bot'),
                'origin' => url('/'),
            ]);

        // For Telegram, redirect() returns the widget HTML as a string
        return $provider->redirect();
    }

    public function getUser(): User
    {
        return $this->provider
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.telegram.redirect'))
            ->user();
    }

    public function canCreateUser(): bool
    {
        return true;
    }

    public function setRedirectUrl(string $url): OAuthDriver
    {
        $this->redirectUrl = $url;
        return $this;
    }

    public function setScopes(array $scopes): OAuthDriver
    {
        $this->scopes = $scopes;
        return $this;
    }

    public function fullScopes(): OAuthDriver
    {
        // Telegram doesn't use scopes
        return $this->setScopes([]);
    }
}
