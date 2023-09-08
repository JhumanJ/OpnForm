<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ValidPhoneInputRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return false;
        }
        if (!Str::startsWith($value, '+')) {
            return false;
        }
        $parts = explode(' ', $value);
        if (count($parts) < 2) {
            return false;
        }
        return strlen($parts[1]) >= 10;
    }

    public function message()
    {
        return 'The :attribute must be a string that starts with a "+" character and must be at least 10 digits long.';
    }
}