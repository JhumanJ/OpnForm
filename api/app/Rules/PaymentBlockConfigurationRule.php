<?php

namespace App\Rules;

use App\Models\OAuthProvider;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentBlockConfigurationRule implements ValidationRule
{
    protected array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value['type'] !== 'nf-payment') {
            return; // If not a payment block, validation passes
        }

        // Only one payment block allowed
        $paymentBlocks = collect($this->properties)
            ->filter(fn($prop) => $prop['type'] === 'nf-payment')
            ->count();

        if ($paymentBlocks > 1) {
            $fail('Only one payment block allowed');
            return;
        }


        // Amount validation
        if (!isset($value['amount']) || !is_numeric($value['amount']) || $value['amount'] < 0.5) {
            $fail('Amount must be a number greater than 0.5');
            return;
        }

        // Currency validation
        if (!isset($value['currency']) || !in_array(strtoupper($value['currency']), array_keys(config('services.stripe.currencies')))) {
            $fail('Currency must be a valid currency');
            return;
        }

        // Stripe account validation
        if (!isset($value['stripe_account_id']) || empty($value['stripe_account_id'])) {
            $fail('Stripe account is required');
            return;
        }
        try {
            $provider = OAuthProvider::where('provider', 'stripe')
                ->where('provider_user_id', $value['stripe_account_id'])
                ->first();

            if ($provider === null) {
                $fail('Failed to validate Stripe account');
                return;
            }
        } catch (\Exception $e) {
            Log::error('Failed to validate Stripe account', [
                'error' => $e->getMessage(),
                'account_id' => $value['stripe_account_id']
            ]);
            $fail('Failed to validate Stripe account');
            return;
        }
    }
}
