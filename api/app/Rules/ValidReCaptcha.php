<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Http;

class ValidReCaptcha implements ImplicitRule
{
    public const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    private $error = 'validation.invalid_captcha';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            $this->error = 'validation.complete_captcha';

            return false;
        }

        return Http::asForm()->post(self::RECAPTCHA_VERIFY_URL, [
            'secret' => config('services.re_captcha.secret_key'),
            'response' => $value,
        ])->json('success');
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
        return trans($this->error);
    }
}
