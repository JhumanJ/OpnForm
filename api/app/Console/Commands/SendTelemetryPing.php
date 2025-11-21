<?php

namespace App\Console\Commands;

use App\Service\Telemetry\TelemetryEvent;
use App\Service\Telemetry\TelemetryService;
use Illuminate\Console\Command;

class SendTelemetryPing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telemetry:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a ping event to track active instances (runs hourly, sends once per day per instance)';

    /**
     * Execute the console command.
     */
    public function handle(TelemetryService $telemetryService): int
    {
        if (!$telemetryService->shouldSendTelemetry()) {
            return Command::SUCCESS;
        }

        $instanceId = $telemetryService->getInstanceId();

        if (!$instanceId) {
            $this->warn('Telemetry ping skipped: instance_id not found');
            return Command::SUCCESS;
        }

        // Use instance_id hash to determine which hour of the day (0-23) this instance should ping
        // This distributes the load across all 24 hours
        // Since the command runs hourly, each instance will only ping once per day during its assigned hour
        $assignedHour = hexdec(substr(md5($instanceId), 0, 2)) % 24;
        $currentHour = (int) now()->format('H');

        // Only send ping if it's the assigned hour for this instance
        if ($assignedHour !== $currentHour) {
            return Command::SUCCESS;
        }

        // Send ping event
        telemetry(TelemetryEvent::INSTANCE_PING);

        $this->info('Telemetry ping sent successfully');

        return Command::SUCCESS;
    }
}
