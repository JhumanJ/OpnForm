<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    const SUBSCRIPTION_PLANS = ['monthly_2022', 'yearly_2022'];

    const PRO_SUBSCRIPTION_NAME = 'default';
    const ENTERPRISE_SUBSCRIPTION_NAME = 'enterprise';

    const SUBSCRIPTION_NAMES = [
        self::PRO_SUBSCRIPTION_NAME,
        self::ENTERPRISE_SUBSCRIPTION_NAME
    ];

    /**
     * Returns stripe checkout URL
     *
     * $plan is constrained with regex in the api.php
     */
    public function checkout($pricing, $plan, $trial = null)
    {
        $this->middleware('not-subscribed');

        // Check User does not have a pending subscription
        $user = Auth::user();
        if ($user->subscriptions()->where('stripe_status', 'past_due')->first()) {
            return $this->error([
                'message' => 'You already have a past due subscription. Please verify your details in the billing page,
                and contact us if the issue persists.'
            ]);
        }

        $checkoutBuilder = $user
            ->newSubscription($pricing, $this->getPricing($pricing)[$plan])
            ->allowPromotionCodes();

        if ($trial != null) {
            $checkoutBuilder->trialDays(3);
        }

        $checkout = $checkoutBuilder
            ->collectTaxIds()
            ->checkout([
                'success_url' => url('/subscriptions/success'),
                'cancel_url' => url('/subscriptions/error'),
            ]);

        return $this->success([
            'checkout_url' => $checkout->url
        ]);
    }

    public function billingPortal()
    {
        $this->middleware('auth');
        if (!Auth::user()->has_customer_id) {
            return $this->error([
                "message" => "Please subscribe before accessing your billing portal."
            ]);
        }
        return $this->success([
            'portal_url' => Auth::user()->billingPortalUrl(url('/home'))
        ]);
    }

    private function getPricing($product = 'pro')
    {
        return App::environment() == 'production' ? config('pricing.production.'.$product.'.pricing') : config('pricing.test.'.$product.'.pricing');
    }
}
