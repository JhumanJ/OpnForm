<?php

namespace App\Jobs\Form;

use App\Console\Commands\GenerateTemplate;
use App\Http\Requests\AiGenerateFormRequest;
use App\Models\Forms\AI\AiFormCompletion;
use App\Service\OpenAi\GptCompleter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateAiForm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
           'status' => AiFormCompletion::STATUS_PROCESSING
        ]);

        $completer = (new GptCompleter(config('services.openai.api_key')))
            ->setSystemMessage('You are a robot helping to generate forms.');

        try {
            $completer->completeChat([
                ["role" => "user", "content" => Str::of(GenerateTemplate::FORM_STRUCTURE_PROMPT)
                    ->replace('[REPLACE]', $this->completion->form_prompt)->toString()]
            ], 3000);

            $this->completion->update([
                'status' => AiFormCompletion::STATUS_COMPLETED,
                'result' => $this->cleanOutput($completer->getArray())
            ]);
        } catch (\Exception $e) {
            $this->completion->update([
                'status' => AiFormCompletion::STATUS_FAILED,
                'result' => ['error' => $e->getMessage()]
            ]);
        }

    }

    public function generateForm(AiGenerateFormRequest $request)
    {
        $completer = (new GptCompleter(config('services.openai.api_key')))
            ->setSystemMessage('You are a robot helping to generate forms.');
        $completer->completeChat([
            ["role" => "user", "content" => Str::of(GenerateTemplate::FORM_STRUCTURE_PROMPT)
                ->replace('[REPLACE]', $request->form_prompt)->toString()]
        ], 3000);

        return $this->success([
            'message' => 'Form successfully generated!',
            'form' => $this->cleanOutput($completer->getArray())
        ]);
    }

    private function cleanOutput($formData)
    {
        // Add property uuids
        foreach ($formData['properties'] as &$property) {
            $property['id'] = Str::uuid()->toString();
        }

        return $formData;
    }
}
