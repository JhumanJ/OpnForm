<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentIntentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow public access for now, protected by middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // No body parameters need validation as they are derived from the form itself.
        return [
            // Keep empty for now, can add header/other validation later if needed.
        ];
    }
}
