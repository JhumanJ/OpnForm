<?php

namespace App\Http\Requests\Integration;

use App\Models\Forms\Form;
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
    private ?Form $form = null;

    public function __construct(Request $request)
    {
        $this->form = Form::findOrFail(request()->route('id'));
        if ($request->integration_id) {
            // Load integration class, and get rules
            $integration = FormIntegration::getIntegration($request->integration_id);
            if ($integration && isset($integration['file_name']) && class_exists(
                'App\Integrations\Handlers\\' . $integration['file_name']
            )) {
                $this->integrationClassName = 'App\Integrations\Handlers\\' . $integration['file_name'];
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
            'oauth_id' => [
                $this->isOAuthRequired() ? 'required' : 'nullable',
                Rule::exists('oauth_providers', 'id')
            ],
            'settings' => 'present|array',
            'status' => 'required|boolean',
            'logic' => [new IntegrationLogicRule()],
        ], $this->integrationRules);
    }

    /**
     * Give the validated fields a better "human-readable" name
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = $this->integrationClassName::getValidationAttributes();

        $fields = [];
        foreach ($this->rules() as $key => $value) {
            $fields[$key] = $attributes[$key] ?? Str::of($key)
                ->replace('settings.', '')
                ->headline()
                ->toString();
        }

        return $fields;
    }

    protected function isOAuthRequired(): bool
    {
        return $this->integrationClassName::isOAuthRequired();
    }

    private function loadIntegrationRules()
    {
        foreach ($this->integrationClassName::getValidationRules($this->form) as $key => $value) {
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
            'logic' => $this->validated('logic') ?? [],
            'oauth_id' => $this->validated('oauth_id'),
        ]);
    }
}
