<?php

use App\Http\Controllers\Content\FeatureFlagsController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

it('returns feature flags', function () {
    // Arrange
    Config::set('app.self_hosted', false);
    Config::set('custom-domains.enabled', true);
    Config::set('cashier.key', 'stripe_key');
    Config::set('cashier.secret', 'stripe_secret');
    Config::set('services.appsumo.api_key', 'appsumo_key');
    Config::set('services.appsumo.api_secret', 'appsumo_secret');
    Config::set('filesystems.default', 's3');
    Config::set('services.openai.api_key', 'openai_key');
    Config::set('services.unsplash.access_key', 'unsplash_key');
    Config::set('services.google.fonts_api_key', 'google_fonts_key');
    Config::set('services.google.client_id', 'google_client_id');
    Config::set('services.google.client_secret', 'google_client_secret');
    Config::set('services.zapier.enabled', true);

    // Act
    $response = $this->getJson(route('content.feature-flags'));

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'self_hosted' => false,
            'custom_domains' => true,
            'ai_features' => true,
            'billing' => [
                'enabled' => true,
                'appsumo' => true,
            ],
            'storage' => [
                'local' => false,
                's3' => true,
            ],
            'services' => [
                'unsplash' => true,
                'google' => [
                    'fonts' => true,
                    'auth' => true,
                ],
            ],
            'integrations' => [
                'zapier' => true,
                'google_sheets' => true,
            ],
        ]);
});

it('caches feature flags', function () {
    // Arrange
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            return $key === 'feature_flags' && $ttl === 3600 && is_callable($callback);
        })
        ->andReturn(['some' => 'data']);

    // Act
    $controller = new FeatureFlagsController();
    $response = $controller->index();

    // Assert
    $this->assertEquals(['some' => 'data'], $response->getData(true));
});
