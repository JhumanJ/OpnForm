<?php

namespace App\Http\Controllers\Forms;

use App\Console\Commands\GenerateTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\AiGenerateFormRequest;
use App\Service\OpenAi\GptCompleter;
use Illuminate\Support\Str;

class AiFormController extends Controller
{
    public function generateForm(AiGenerateFormRequest $request)
    {
        $this->middleware('throttle:4,1');
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
