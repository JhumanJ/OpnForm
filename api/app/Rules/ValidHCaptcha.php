<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Http;

class ValidHCaptcha implements ImplicitRule
{
    public const H_CAPTCHA_VERIFY_URL = 'https://hcaptcha.com/siteverify';

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

        return Http::asForm()->post(self::H_CAPTCHA_VERIFY_URL, [
            'secret' => config('services.h_captcha.secret_key'),
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
