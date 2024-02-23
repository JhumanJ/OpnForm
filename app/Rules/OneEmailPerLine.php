<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OneEmailPerLine implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value === null || empty(trim($value))) {
            return true;
        }
        foreach (preg_split("/\r\n|\n|\r/", $value) as $email) {
            $email = trim($email);
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You need one valid email per line.';
    }
}
