<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
