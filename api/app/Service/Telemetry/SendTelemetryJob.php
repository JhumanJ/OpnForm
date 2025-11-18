<?php

namespace App\Service\Telemetry;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelemetryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $eventName,
        public array $properties
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TelemetryService $telemetryService, OpenPanelClient $client): void
    {
        if (!$telemetryService->shouldSendTelemetry()) {
            return;
        }

        try {
            $instanceId = $telemetryService->getInstanceId();

            if (!$instanceId) {
                Log::warning('Telemetry skipped: instance_id not found');
                return;
            }

            $endpoint = $telemetryService->getEndpoint();
            $clientId = $telemetryService->getClientId();
            $clientSecret = $telemetryService->getClientSecret();

            $client->sendEvent(
                $this->eventName,
                $this->properties,
                $endpoint,
                $clientId,
                $clientSecret,
                $instanceId
            );
        } catch (\Exception $e) {
            Log::warning('Telemetry job error', [
                'event' => $this->eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
