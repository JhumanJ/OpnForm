<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use App\Models\User;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function getEmail($userId)
    {
        $user  = User::find($userId);

        if (!$user->hasStripeId()) {
            return $this->error([
                "message" => "Stripe user not created",
                "billing_email" => null
            ]);
        }

        $user = $user->asStripeCustomer();

        return $this->success([
            'billing_email'  =>  $user->email
        ]);
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'billing_email' => 'required|email'
        ]);

        $user = User::find($request->get("user_id"));

        if (!$user->hasStripeId()) {
            return $this->error([
                "message" => "Stripe user not created",
                "billing_email" => null
            ]);
        }
        $user->updateStripeCustomer(['email' => $request->billing_email]);

        return $this->success(['message' => 'Billing email updated successfully']);
    }

    public function getSubscriptions($userId)
    {
        $user  = User::find($userId);
        if (!$user->hasStripeId()) {
            return $this->error([
                "message" => "Stripe user not created",
                "billing_email" => null
            ]);
        }
        $subscriptions = $user->subscriptions->map(function($subscription) use ($user){
            return  [
                "id" => $subscription->id,
                "stripe_id" => $subscription->stripe_id,
                "name" => ucfirst($user->name),
                "plan" => $subscription->name,
                "status" => $subscription->stripe_status,
                "creation_date" => $subscription->created_at->format('Y-m-d')
            ];
        });
        return $this->success([
            'subscriptions'  =>  $subscriptions,
        ]);
    }

    private function getSubscriptionName(string $stripeProductId)
    {
        $config = App::environment() == 'production' ? config('pricing.production') : config('pricing.test');
        foreach ($config as $plan => $data) {
            if ($stripeProductId == $config[$plan]['product_id']) {
                return $plan;
            }
        }

        return 'default';
    }
}
