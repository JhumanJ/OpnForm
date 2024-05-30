<?php

namespace App\Models\Billing;

use App\Events\SubscriptionCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => SubscriptionCreated::class,
    ];

    public static function booted(): void
    {
        static::saved(function (Subscription $sub) {
            $sub->user->flushCache();
        });
        static::deleted(function (Subscription $sub) {
            $sub->user->flushCache();
        });
    }
}
