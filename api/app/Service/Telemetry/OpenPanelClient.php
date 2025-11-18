<?php

namespace App\Service\Telemetry;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenPanelClient
{
    public function __construct(
        private string $endpoint,
        private ?string $clientId,
        private ?string $clientSecret
    ) {
    }

    /**
     * Send an event to OpenPanel.
     *
     * @param string $eventName
     * @param array $properties
     * @param string|null $instanceId
     * @return bool
     */
    public function sendEvent(
        string $eventName,
        array $properties,
        ?string $instanceId
    ): bool {
        if (!$this->clientId || !$this->clientSecret) {
            Log::warning('Telemetry skipped: missing client credentials');
            return false;
        }

        if (!$instanceId) {
            Log::warning('Telemetry skipped: instance_id not found');
            return false;
        }

        try {
            $payload = [
                'type' => 'track',
                'payload' => [
                    'name' => $eventName,
                    'properties' => array_merge($properties, [
                        'instance_id' => $instanceId,
                    ]),
                ],
            ];

            $response = Http::timeout(5)
                ->withHeaders([
                    'openpanel-client-id' => $this->clientId,
                    'openpanel-client-secret' => $this->clientSecret,
                ])
                ->post($this->endpoint, $payload);

            if (!$response->successful()) {
                Log::warning('Telemetry event failed to send', [
                    'event' => $eventName,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning('Telemetry event error', [
                'event' => $eventName,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
