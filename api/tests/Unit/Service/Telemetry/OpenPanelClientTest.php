<?php

use App\Service\Telemetry\OpenPanelClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

describe('OpenPanelClient', function () {
    beforeEach(function () {
        $this->client = new OpenPanelClient();
    });

    it('can be instantiated', function () {
        expect($this->client)->toBeInstanceOf(OpenPanelClient::class);
    });

    it('sends event successfully with valid credentials', function () {
        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $result = $this->client->sendEvent(
            'test.event',
            ['foo' => 'bar'],
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret',
            'instance-id'
        );

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://test-endpoint.com/track'
                && $request->hasHeader('openpanel-client-id', 'client-id')
                && $request->hasHeader('openpanel-client-secret', 'client-secret')
                && $request['type'] === 'track'
                && $request['payload']['name'] === 'test.event'
                && $request['payload']['properties']['foo'] === 'bar'
                && $request['payload']['properties']['instance_id'] === 'instance-id';
        });
    });

    it('returns false when client id is missing', function () {
        $result = $this->client->sendEvent(
            'test.event',
            [],
            'https://test-endpoint.com/track',
            null,
            'client-secret',
            'instance-id'
        );

        expect($result)->toBeFalse();
    });

    it('returns false when client secret is missing', function () {
        $result = $this->client->sendEvent(
            'test.event',
            [],
            'https://test-endpoint.com/track',
            'client-id',
            null,
            'instance-id'
        );

        expect($result)->toBeFalse();
    });

    it('handles HTTP errors gracefully', function () {
        Log::spy();

        Http::fake([
            'test-endpoint.com/track' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $result = $this->client->sendEvent(
            'test.event',
            [],
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret',
            'instance-id'
        );

        expect($result)->toBeFalse();
        Log::shouldHaveReceived('warning')
            ->with('Telemetry event failed to send', \Mockery::type('array'))
            ->once();
    });

    it('handles exceptions gracefully', function () {
        Log::spy();

        Http::fake(function () {
            throw new \Exception('Network error');
        });

        $result = $this->client->sendEvent(
            'test.event',
            [],
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret',
            'instance-id'
        );

        expect($result)->toBeFalse();
        Log::shouldHaveReceived('warning')
            ->with('Telemetry event error', \Mockery::type('array'))
            ->once();
    });

    it('includes instance id in event properties', function () {
        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $this->client->sendEvent(
            'test.event',
            ['custom' => 'property'],
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret',
            'test-instance-id'
        );

        Http::assertSent(function ($request) {
            $properties = $request['payload']['properties'];
            return $properties['instance_id'] === 'test-instance-id'
                && $properties['custom'] === 'property';
        });
    });

    it('works without instance id', function () {
        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $result = $this->client->sendEvent(
            'test.event',
            [],
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret',
            null
        );

        expect($result)->toBeTrue();

        Http::assertSent(function ($request) {
            return $request['payload']['properties']['instance_id'] === null;
        });
    });
});
