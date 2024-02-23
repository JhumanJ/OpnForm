<?php

namespace App\Http\Requests\Integration;

use App\Models\Integration\FormIntegrations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormIntegrationsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'integration_id' => ['required', Rule::in(array_keys(FormIntegrations::getAllIntegrations()))],
            'settings' => 'required'
        ];
    }
}
