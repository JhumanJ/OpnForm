<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use App\Integrations\OAuth\Drivers\Contracts\SupportsEmailRestrictions as SupportsEmailRestrictionsContract;
use App\Integrations\OAuth\Drivers\Traits\HasEmailRestrictions;
use Google\Service\Sheets;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class OAuthGoogleDriver implements OAuthDriver, SupportsEmailRestrictionsContract
{
    use HasEmailRestrictions;

    private ?string $redirectUrl = null;
    private ?array $scopes = [];

    protected GoogleProvider $provider;

    public function __construct()
    {
        $this->provider = Socialite::driver('google');
    }

    public function getRedirectUrl(): string
    {
        $parameters = [
            'access_type' => 'offline',
            'prompt' => 'consent select_account'
        ];

        // Merge email restriction parameters from trait
        $parameters = array_merge($parameters, $this->getEmailRestrictionParameters());

        return $this->provider
            ->scopes($this->scopes ?? [])
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.google.redirect'))
            ->with($parameters)
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
