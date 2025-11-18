<?php

use App\Models\Setting;
use App\Models\SettingsKey;
use App\Service\Telemetry\SendTelemetryJob;
use App\Service\Telemetry\TelemetryEvent;
use App\Service\Telemetry\TelemetryService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

describe('SendTelemetryJob', function () {
    beforeEach(function () {
        Config::set('telemetry.enabled', true);
        Config::set('app.self_hosted', true); // Enable self-hosted for tests
        Config::set('telemetry.endpoint', 'https://test-endpoint.com/track');
        Config::set('telemetry.client_id', 'test-client-id');
        Config::set('telemetry.client_secret', 'test-client-secret');
    });

    it('sends event when telemetry is enabled', function () {
        $instanceId = 'test-instance-id';
        Setting::set(SettingsKey::INSTANCE_ID, $instanceId);

        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $job = new SendTelemetryJob(
            TelemetryEvent::FORM_CREATED->value(),
            []
        );

        $job->handle(
            app(TelemetryService::class),
            app(\App\Service\Telemetry\OpenPanelClient::class)
        );

        Http::assertSent(function ($request) {
            return $request->url() === 'https://test-endpoint.com/track'
                && $request['type'] === 'track'
                && $request['payload']['name'] === TelemetryEvent::FORM_CREATED->value();
        });
    });

    it('does not send event when telemetry is disabled', function () {
        Config::set('telemetry.enabled', false);
        $instanceId = 'test-instance-id';
        Setting::set(SettingsKey::INSTANCE_ID, $instanceId);

        Http::fake();

        $job = new SendTelemetryJob(
            TelemetryEvent::FORM_CREATED->value(),
            []
        );

        $job->handle(
            app(TelemetryService::class),
            app(\App\Service\Telemetry\OpenPanelClient::class)
        );

        Http::assertNothingSent();
    });

    it('does not send event when instance id is missing', function () {
        Setting::where('key', SettingsKey::INSTANCE_ID->value)->delete();

        Http::fake();

        $job = new SendTelemetryJob(
            TelemetryEvent::FORM_CREATED->value(),
            []
        );

        $job->handle(
            app(TelemetryService::class),
            app(\App\Service\Telemetry\OpenPanelClient::class)
        );

        Http::assertNothingSent();
    });

    it('includes instance id in event properties', function () {
        $instanceId = 'test-instance-id';
        Setting::set(SettingsKey::INSTANCE_ID, $instanceId);

        Http::fake([
            'test-endpoint.com/track' => Http::response([], 200),
        ]);

        $job = new SendTelemetryJob(
            TelemetryEvent::FORM_CREATED->value(),
            ['custom' => 'property']
        );

        $job->handle(
            app(TelemetryService::class),
            app(\App\Service\Telemetry\OpenPanelClient::class)
        );

        Http::assertSent(function ($request) use ($instanceId) {
            $properties = $request['payload']['properties'];
            return $properties['instance_id'] === $instanceId
                && $properties['custom'] === 'property';
        });
    });

    it('handles errors gracefully', function () {
        $instanceId = 'test-instance-id';
        Setting::set(SettingsKey::INSTANCE_ID, $instanceId);

        Http::fake(function () {
            throw new \Exception('Network error');
        });

        $job = new SendTelemetryJob(
            TelemetryEvent::FORM_CREATED->value(),
            []
        );

        $job->handle(
            app(TelemetryService::class),
            app(\App\Service\Telemetry\OpenPanelClient::class)
        );

        // Should not throw exception
        expect(true)->toBeTrue();
    });
});
