<?php

namespace App\Jobs\Form;

use App\Console\Commands\GenerateTemplate;
use App\Models\Forms\AI\AiFormCompletion;
use App\Service\OpenAi\GptCompleter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateAiForm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public AiFormCompletion $completion)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->completion->update([
            'status' => AiFormCompletion::STATUS_PROCESSING,
        ]);

        $completer = (new GptCompleter(config('services.openai.api_key')))
            ->useStreaming()
            ->setSystemMessage('You are a robot helping to generate forms.')
            ->expectsJson();

        try {
            $completer->completeChat([
                ['role' => 'user', 'content' => Str::of(GenerateTemplate::FORM_STRUCTURE_PROMPT)
                    ->replace('[REPLACE]', $this->completion->form_prompt)->toString()],
            ], 3000);

            $this->completion->update([
                'status' => AiFormCompletion::STATUS_COMPLETED,
                'result' => GenerateTemplate::cleanAiOutput($completer->getArray())
            ]);
        } catch (\Exception $e) {
            $this->onError($e);
        }

    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->onError($exception);
    }

    private function onError(\Throwable $e)
    {
        $this->completion->update([
            'status' => AiFormCompletion::STATUS_FAILED,
            'result' => ['error' => $e->getMessage()],
        ]);
    }
}
