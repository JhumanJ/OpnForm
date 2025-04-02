<?php

namespace App\Console\Commands;

use App\Jobs\Template\GenerateTemplateJob;
use Illuminate\Console\Command;

class GenerateTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'form:generate {prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new form template from a prompt';

    public $generatedTemplate;

    /**
     * Execute the command.
     *
     * @return int
     */
    public function handle()
    {
        $job = new GenerateTemplateJob($this->argument('prompt'));
        $job->handle();

        $this->generatedTemplate = $job->generatedTemplate;

        if ($this->generatedTemplate) {
            $this->info(front_url('/templates/' . $this->generatedTemplate->slug));
            return Command::SUCCESS;
        }

        $this->error('Failed to generate template');
        return Command::FAILURE;
    }
}
