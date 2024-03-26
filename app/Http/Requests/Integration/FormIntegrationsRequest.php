<?php

namespace App\Http\Requests\Integration;

use App\Models\Integration\FormIntegration;
use App\Rules\IntegrationLogicRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FormIntegrationsRequest extends FormRequest
{

    public array $integrationRules = [];

    public function __construct(Request $request)
    {
        if ($request->integration_id) {
            $this->loadIntegrationRules($request->integration_id);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'integration_id' => ['required', Rule::in(array_keys(FormIntegration::getAllIntegrations()))],
            'settings' => 'present|array',
            'logic' => [new IntegrationLogicRule()]
        ], $this->integrationRules);
    }

    /**
     * Give the validated fields a better "human-readable" name
     *
     * @return array
     */
    public function attributes()
    {
        $fields = [];
        foreach ($this->rules() as $key => $value) {
            $fields[$key] = Str::of($key)
                ->replace('settings.', '')
                ->headline();
        }

        return $fields;
    }

    private function loadIntegrationRules(string $integrationId)
    {
        $integration = FormIntegration::getIntegration($integrationId);
        if ($integration && isset($integration['file_name']) && class_exists(
                'App\Service\Forms\Integrations\\' . $integration['file_name']
            )) {
            $className = 'App\Service\Forms\Integrations\\' . $integration['file_name'];
            foreach ($className::getValidationRules() as $key => $value) {
                $this->integrationRules['settings.' . $key] = $value;
            }
            return;
        }
        throw new \Exception('Unknown Integration!');
    }
}
