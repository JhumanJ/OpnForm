<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Contracts\WidgetOAuthDriver;
use Laravel\Socialite\Contracts\User;

class OAuthTelegramDriver implements WidgetOAuthDriver
{
    protected string $redirectUrl;
    protected array $scopes = [];

    public function getRedirectUrl(): string
    {
        return '';  // Not used for widget-based auth
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

    public function getUser(): User
    {
        throw new \Exception('Use getUserFromWidgetData for Widget based authentication');
    }

    public function canCreateUser(): bool
    {
        return true;
    }

    public function fullScopes(): self
    {
        return $this;
    }

    public function isWidgetBased(): bool
    {
        return true;
    }

    public function verifyWidgetData(array $data): bool
    {
        $checkHash = $data['hash'];
        unset($data['hash']);

        $dataCheckArr = [];
        foreach ($data as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }

        sort($dataCheckArr);
        $dataCheckString = implode("\n", $dataCheckArr);
        $secretKey = hash('sha256', config('services.telegram.bot_token'), true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $checkHash);
    }

    public function getUserFromWidgetData(array $data): array
    {
        return [
            'id' => $data['id'],
            'name' => trim($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
            'email' => $data['email'] ?? null,
            'provider_user_id' => $data['id'],
            'provider' => 'telegram',
            'access_token' => $data['hash'],
            'avatar' => $data['photo_url'] ?? null,
            'scopes' => []
        ];
    }
}
