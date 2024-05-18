<?php

namespace App\Integrations\Google;

use App\Integrations\Google\Sheets\SpreadsheetManager;
use App\Models\OAuthProvider;
use Google\Client as Client;

class Google
{
    protected Client $client;
    protected ?string $token;
    protected ?string $refreshToken;

    public function __construct(
        protected OAuthProvider $provider
    ) {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setAccessToken($this->provider->access_token);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function refreshToken(): static
    {
        [$accessToken, $refreshToken] = $this->client->refreshToken($this->provider->refresh_token);

        $this->provider->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);

        return $this;
    }

    public function sheets(): SpreadsheetManager
    {
        return new SpreadsheetManager($this);
    }
}
