<?php

namespace App\Service;

use Illuminate\Support\Facades\App;
use Stripe\SubscriptionItem;

class BillingHelper
{
    public static function getPricing($productName = 'default')
    {
        return App::environment() == 'production' ?
            config('pricing.production.' . $productName . '.pricing') :
            config('pricing.test.' . $productName . '.pricing');
    }

    public static function getProductId($productName = 'default')
    {
        return App::environment() == 'production' ?
            config('pricing.production.' . $productName . '.product_id') :
            config('pricing.test.' . $productName . '.product_id');
    }

    public static function getLineItemInterval(SubscriptionItem $item)
    {
        return $item->price->recurring->interval === 'year' ? 'yearly' : 'monthly';
        ;
    }
}
