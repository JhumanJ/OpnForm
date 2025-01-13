<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use App\Models\OAuthProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class FormPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createIntent(Request $request)
    {
        $form = $request->form;

        // Verify form exists and is accessible
        if ($form->workspace === null || $form->visibility !== 'public') {
            Log::warning('Attempt to create payment for invalid form', [
                'form_id' => $form->id
            ]);
            return $this->error(['message' => 'Form not found.'], 404);
        }

        // Get payment block (only one allowed)
        $paymentBlock = collect($form->properties)->first(fn ($prop) => $prop['type'] === 'nf-payment');
        if (!$paymentBlock) {
            Log::warning('Attempt to create payment for form without payment block', [
                'form_id' => $form->id
            ]);
            return $this->error(['message' => 'Invalid form configuration.'], 400);
        }

        // Get provider
        $provider = OAuthProvider::find($paymentBlock['stripe_account_id']);
        if ($provider === null) {
            Log::error('Failed to find Stripe account', [
                'stripe_account_id' => $paymentBlock['stripe_account_id']
            ]);
            return $this->error(['message' => 'Failed to find Stripe account'], 400);
        }

        try {
            Log::info('Creating payment intent', [
                'form_id' => $form->id,
                'amount' => $paymentBlock['amount'],
                'currency' => $paymentBlock['currency']
            ]);

            Stripe::setApiKey(config('cashier.secret'));

            $intent = PaymentIntent::create([
                'amount' => (int) ($paymentBlock['amount'] * 100),  // Stripe requires amount in cents
                'currency' => $paymentBlock['currency'],
                'payment_method_types' => ['card'],
                'metadata' => [
                    'form_id' => $form->id,
                    'workspace_id' => $form->workspace_id,
                    'form_name' => $form->title,
                ],
            ], [
                'stripe_account' => $provider->provider_user_id
            ]);

            Log::info('Payment intent created', [
                'form_id' => $form->id,
                'intent' => $intent
            ]);

            if ($intent->id) {
                return $this->success([
                    'intent' => ['id' => $intent->id, 'secret' => $intent->client_secret]
                ]);
            } else {
                return $this->error(['message' => 'Failed to create payment intent']);
            }
        } catch (\Stripe\Exception\CardException $e) {
            Log::warning('Card payment failed', [
                'form_id' => $form->id,
                'error_code' => $e->getStripeCode()
            ]);
            return $this->error(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment intent', [
                'form_id' => $form->id,
                'error' => $e->getMessage()
            ]);
            return $this->error(['message' => 'Failed to initialize payment.']);
        }
    }
}
