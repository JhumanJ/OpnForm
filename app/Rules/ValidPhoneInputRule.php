<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ValidPhoneInputRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (!is_string($value) || !Str::startsWith($value, '+')) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return 'The :attribute is invalid.';
    }
}