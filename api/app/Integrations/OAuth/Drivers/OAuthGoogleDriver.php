<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Traits\SupportsEmailRestrictions;
use Google\Service\Sheets;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class OAuthGoogleDriver extends BaseOAuthDriver
{
    use SupportsEmailRestrictions;

    private ?string $redirectUrl = null;
    private ?array $scopes = [];

    protected GoogleProvider $provider;

    public function __construct()
    {
        $this->provider = Socialite::driver('google');
    }

    public function getRedirectUrl(): string
    {
        $baseParameters = [
            'access_type' => 'offline',
            'prompt' => 'consent select_account'
        ];

        // Merge email restriction parameters from trait
        $emailParameters = $this->getEmailRestrictionParameters();

        // Merge additional parameters from base class
        $additionalParameters = $this->getAdditionalParameters();

        $allParameters = array_merge($baseParameters, $emailParameters, $additionalParameters);

        return $this->provider
            ->scopes($this->scopes ?? [])
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.google.redirect'))
            ->with($allParameters)
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

    public function setRedirectUrl(string $url): self
    {
        $this->redirectUrl = $url;
        return $this;
    }

    public function setScopes(array $scopes): self
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
}
