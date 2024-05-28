<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiGenerateFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'form_prompt' => 'required|string|max:1000',
        ];
    }
}
