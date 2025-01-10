<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SocialiteProviders\Stripe\Provider as StripeProvider;

class OAuthStripeDriver implements OAuthDriver
{
    private ?string $redirectUrl = null;
    private ?array $scopes = [];

    protected StripeProvider $provider;

    public function __construct()
    {
        $this->provider = Socialite::driver('stripe');
    }

    public function getRedirectUrl(): string
    {
        $user = Auth::user();

        $params = [
            'stripe_user[email]' => $user->email,
            'stripe_user[url]' => config('app.url'),
            'stripe_user[business_name]' => $user->name,
        ];

        Log::info('Initiating Stripe Connect flow', [
            'user_id' => $user->id
        ]);

        return $this->provider
            ->scopes($this->scopes ?? [])
            ->with($params)
            ->redirect()
            ->getTargetUrl();
    }

    public function getUser(): User
    {
        return $this->provider
            ->stateless()
            ->redirectUrl($this->redirectUrl ?? config('services.stripe.redirect'))
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
        return $this->setScopes([
            'read_write',
        ]);
    }
}
