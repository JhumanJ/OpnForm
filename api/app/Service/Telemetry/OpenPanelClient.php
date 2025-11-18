<?php

namespace App\Service\Telemetry;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenPanelClient
{
    /**
     * Send an event to OpenPanel.
     *
     * @param string $eventName
     * @param array $properties
     * @param string $endpoint
     * @param string|null $clientId
     * @param string|null $clientSecret
     * @param string|null $instanceId
     * @return bool
     */
    public function sendEvent(
        string $eventName,
        array $properties,
        string $endpoint,
        ?string $clientId,
        ?string $clientSecret,
        ?string $instanceId
    ): bool {
        if (!$clientId || !$clientSecret) {
            Log::warning('Telemetry skipped: missing client credentials');
            return false;
        }

        try {
            // OpenPanel API format: https://openpanel.dev/docs/api/track
            $payload = [
                'type' => 'track',
                'payload' => [
                    'name' => $eventName,
                    'properties' => array_merge($properties, [
                        'instance_id' => $instanceId,
                    ]),
                ],
            ];

            // If we have an instance_id, identify it in the event
            if ($instanceId) {
                $payload['payload']['properties']['__identify'] = [
                    'profileId' => $instanceId,
                ];
            }

            $response = Http::timeout(5)
                ->withHeaders([
                    'openpanel-client-id' => $clientId,
                    'openpanel-client-secret' => $clientSecret,
                ])
                ->post($endpoint, $payload);

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

