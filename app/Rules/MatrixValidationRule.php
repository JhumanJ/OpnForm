<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MatrixValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('The Matrix field must be an array.');
            return;
        }
        $nullValues = array_filter($value, function ($val) {
            return $val === null;
        });
        if (sizeof($nullValues)) {
            $fail('The Matrix field is required.');
        }
    }
}