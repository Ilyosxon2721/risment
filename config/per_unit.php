<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Per-Unit Pricing Surcharge
    |--------------------------------------------------------------------------
    |
    | Non-subscription surcharge applied to FBS operational costs (Pick&Pack + Delivery)
    | when client uses штучный тариф instead of subscription packages.
    | This makes packages 10-20% more profitable, encouraging subscriptions.
    |
    */

    'surcharge_default' => env('PER_UNIT_SURCHARGE_DEFAULT', 0.10), // 10%
    'surcharge_peak' => env('PER_UNIT_SURCHARGE_PEAK', 0.20), // 20%
    'surcharge_peak_threshold' => env('PER_UNIT_SURCHARGE_PEAK_THRESHOLD', 300), // shipments/month

    /*
    |--------------------------------------------------------------------------
    | Rounding
    |--------------------------------------------------------------------------
    |
    | Round per-unit rates up to nearest multiple for cleaner pricing.
    |
    */

    'rate_rounding' => env('PER_UNIT_RATE_ROUNDING', 1000), // Round to nearest 1,000
];
