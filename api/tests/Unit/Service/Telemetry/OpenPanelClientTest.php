<?php

use App\Service\Telemetry\OpenPanelClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

describe('OpenPanelClient', function () {
    it('can be instantiated', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret'
        );

        expect($client)->toBeInstanceOf(OpenPanelClient::class);
    });

    it('sends event successfully with valid credentials', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret'
        );

        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $result = $client->sendEvent(
            'test.event',
            ['foo' => 'bar'],
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
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            null,
            'client-secret'
        );

        $result = $client->sendEvent(
            'test.event',
            [],
            'instance-id'
        );

        expect($result)->toBeFalse();
    });

    it('returns false when client secret is missing', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            null
        );

        $result = $client->sendEvent(
            'test.event',
            [],
            'instance-id'
        );

        expect($result)->toBeFalse();
    });

    it('handles HTTP errors gracefully', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret'
        );

        Log::spy();

        Http::fake([
            'test-endpoint.com/track' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $result = $client->sendEvent(
            'test.event',
            [],
            'instance-id'
        );

        expect($result)->toBeFalse();
        Log::shouldHaveReceived('warning')
            ->with('Telemetry event failed to send', \Mockery::type('array'))
            ->once();
    });

    it('handles exceptions gracefully', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret'
        );

        Log::spy();

        Http::fake(function () {
            throw new \Exception('Network error');
        });

        $result = $client->sendEvent(
            'test.event',
            [],
            'instance-id'
        );

        expect($result)->toBeFalse();
        Log::shouldHaveReceived('warning')
            ->with('Telemetry event error', \Mockery::type('array'))
            ->once();
    });

    it('includes instance id in event properties', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret'
        );

        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $client->sendEvent(
            'test.event',
            ['custom' => 'property'],
            'test-instance-id'
        );

        Http::assertSent(function ($request) {
            $properties = $request['payload']['properties'];
            return $properties['instance_id'] === 'test-instance-id'
                && $properties['custom'] === 'property';
        });
    });

    it('returns false when instance id is missing', function () {
        $client = new OpenPanelClient(
            'https://test-endpoint.com/track',
            'client-id',
            'client-secret'
        );

        Http::fake();

        $result = $client->sendEvent(
            'test.event',
            [],
            null
        );

        expect($result)->toBeFalse();
    });
});
