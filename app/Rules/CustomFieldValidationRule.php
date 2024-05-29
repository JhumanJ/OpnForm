<?php

namespace App\Rules;

use App\Service\Forms\FormLogicConditionChecker;
use Illuminate\Contracts\Validation\Rule;

class CustomFieldValidationRule implements Rule
{
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
        if (!($this->validation['error_conditions']['conditions'] ?? null) || is_null(
                $this->validation['error_conditions']['conditions'] ?? null
            )) {
        return true;
    }
        return FormLogicConditionChecker::conditionsMet(
            $this->validation['error_conditions']['conditions'],
            $this->formData
        );
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
