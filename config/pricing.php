<?php

return [

    'production' => [
        'default' => [
            'product_id' => env('STRIPE_PROD_DEFAULT_PRODUCT_ID'),
            'pricing' => [
                'monthly' => env('STRIPE_PROD_DEFAULT_PRICING_MONTHLY'),
                'yearly' => env('STRIPE_PROD_DEFAULT_PRICING_YEARLY'),
            ],
        ],
    ],

    'test' => [
        'default' => [
            'product_id' => env('STRIPE_TEST_DEFAULT_PRODUCT_ID'),
            'pricing' => [
                'monthly' => env('STRIPE_TEST_DEFAULT_PRICING_MONTHLY'),
                'yearly' => env('STRIPE_TEST_DEFAULT_PRICING_YEARLY'),
            ],
        ],
    ],

];
