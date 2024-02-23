<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\AiGenerateFormRequest;
use App\Models\Forms\AI\AiFormCompletion;

class AiFormController extends Controller
{
    public function generateForm(AiGenerateFormRequest $request)
    {
        $this->middleware('throttle:4,1');

        return $this->success([
            'message' => 'We\'re working on your form, please wait ~1 min.',
            'ai_form_completion_id' => AiFormCompletion::create([
                'form_prompt' => $request->input('form_prompt'),
                'ip' => $request->ip(),
            ])->id,
        ]);
    }

    public function show(AiFormCompletion $aiFormCompletion)
    {
        if ($aiFormCompletion->ip != request()->ip()) {
            return $this->error('You are not authorized to view this AI completion.', 403);
        }

        return $this->success([
            'ai_form_completion' => $aiFormCompletion,
        ]);
    }
}
