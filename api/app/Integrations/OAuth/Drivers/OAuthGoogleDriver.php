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
    private ?array $scopes = [];
    private array $allowedEmails = [];

    protected GoogleProvider $provider;

    public function __construct()
    {
        $this->provider = Socialite::driver('google');
    }

    public function getRedirectUrl(): string
    {
        $additionalParams = [
            'access_type' => 'offline',
            'prompt' => 'consent select_account'
        ];

        // Add login hint for specific email (use first allowed email if available)
        if (!empty($this->allowedEmails)) {
            $additionalParams['login_hint'] = $this->allowedEmails[0];
        }

        return $this->provider
            ->scopes($this->scopes ?? [])
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.google.redirect'))
            ->with($additionalParams)
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

    public function getScopesForIntent(string $intent): array
    {
        return match ($intent) {
            'auth' => ['openid', 'profile', 'email'],
            'integration' => ['openid', 'profile', 'email', Sheets::DRIVE_FILE],
            default => ['openid', 'profile', 'email'],
        };
    }

    // Set email restrictions for Google OAuth
    public function setEmailRestrictions(array $allowedEmails = []): self
    {
        $this->allowedEmails = $allowedEmails;
        return $this;
    }
}
