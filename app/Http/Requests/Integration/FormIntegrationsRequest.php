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

    private ?string $integrationClassName = null;

    public function __construct(Request $request)
    {
        if ($request->integration_id) {
            // Load integration class, and get rules
            $integration = FormIntegration::getIntegration($request->integration_id);
            if ($integration && isset($integration['file_name']) && class_exists(
                'App\Service\Forms\Integrations\\' . $integration['file_name']
            )) {
                $this->integrationClassName = 'App\Service\Forms\Integrations\\' . $integration['file_name'];
                $this->loadIntegrationRules();
                return;
            }
            throw new \Exception('Unknown Integration!');
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
            'status' => 'required|boolean',
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

    private function loadIntegrationRules()
    {
        foreach ($this->integrationClassName::getValidationRules() as $key => $value) {
            $this->integrationRules['settings.' . $key] = $value;
        }
    }

    public function toIntegrationData(): array
    {
        return $this->integrationClassName::formatData([
            'status' => ($this->validated(
                'status'
            )) ? FormIntegration::STATUS_ACTIVE : FormIntegration::STATUS_INACTIVE,
            'integration_id' => $this->validated('integration_id'),
            'data' => $this->validated('settings') ?? [],
            'logic' => $this->validated('logic') ?? []
        ]);
    }
}
