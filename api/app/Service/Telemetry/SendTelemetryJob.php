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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $eventName,
        public array $properties
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(TelemetryService $telemetryService): void
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

            $client = $telemetryService->createClient();

            $client->sendEvent(
                $this->eventName,
                $this->properties,
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
