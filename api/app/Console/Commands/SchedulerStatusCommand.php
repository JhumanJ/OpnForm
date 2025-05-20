<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SchedulerStatusCommand extends Command
{
    private const MODE_CHECK = 'check';
    private const MODE_RECORD = 'record';
    private const CACHE_KEY_LAST_RUN = 'scheduler_last_run_timestamp';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scheduler-status {--mode=' . self::MODE_CHECK . ' : Mode of operation ("' . self::MODE_CHECK . '" or "' . self::MODE_RECORD . '")} {--max-minutes=3 : Maximum minutes since last run for check mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Records or checks the scheduler last run timestamp, conditional on self-hosted mode.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $isSelfHosted = config('app.self_hosted', false);

        if (!$isSelfHosted) {
            $this->error('This command is only functional in self-hosted mode. Please set SELF_HOSTED=true in your .env file.');
            Log::warning('SchedulerStatusCommand: Attempted to run in non-self-hosted mode.');
            return \Illuminate\Console\Command::FAILURE;
        }

        $mode = $this->option('mode');

        if ($mode === self::MODE_RECORD) {
            Cache::put(self::CACHE_KEY_LAST_RUN, Carbon::now()->getTimestamp(), Carbon::now()->addHours(2));
            $this->info('Scheduler last run timestamp recorded.');
            Log::info('SchedulerStatusCommand: Recorded last run timestamp.');
            return \Illuminate\Console\Command::SUCCESS;
        }

        // Default to 'check' mode (this covers explicit 'check' or any other value for mode)

        $lastRunTimestamp = Cache::get(self::CACHE_KEY_LAST_RUN);
        if (!$lastRunTimestamp) {
            $this->error('Scheduler last run timestamp not found.');
            Log::warning('SchedulerStatusCommand: Last run timestamp not found during check.');
            return \Illuminate\Console\Command::FAILURE;
        }

        $maxMinutes = (int) $this->option('max-minutes');
        if (Carbon::now()->getTimestamp() - $lastRunTimestamp > $maxMinutes * 60) {
            $this->error("Scheduler last ran more than {$maxMinutes} minutes ago. Last run: " . Carbon::createFromTimestamp($lastRunTimestamp)->diffForHumans());
            Log::warning("SchedulerStatusCommand: Health check failed. Last ran more than {$maxMinutes} minutes ago.");
            return \Illuminate\Console\Command::FAILURE;
        }

        $this->info('Scheduler is healthy. Last ran: ' . Carbon::createFromTimestamp($lastRunTimestamp)->diffForHumans());
        return \Illuminate\Console\Command::SUCCESS;
    }
}
