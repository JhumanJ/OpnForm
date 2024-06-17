<?php

namespace App\Rules;

use App\Service\Forms\FormLogicConditionChecker;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CustomFieldValidationRule implements ValidationRule
{
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(public array $validation, public array $formData)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $logicConditions = $this->validation['error_conditions']['conditions'] ?? null;
        if (empty($logicConditions) || empty($logicConditions['children'] ?? [])) {
            return true;
        }

        return FormLogicConditionChecker::conditionsMet(
            $logicConditions,
            $this->formData
        );
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return isset($this->validation['error_message']) ? $this->validation['error_message'] : 'Invalid input';
    }
}
