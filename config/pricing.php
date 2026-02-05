<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Per-Unit Surcharge Configuration
    |--------------------------------------------------------------------------
    |
    | Surcharge rates applied to per-unit (razoviy) tariff operational fees.
    | Surcharge applies ONLY to (Pick&Pack + Delivery), NOT to storage/inbound.
    |
    */
    'per_unit_surcharge_default' => env('PRICING_SURCHARGE_DEFAULT', 0.10), // 10%
    'per_unit_surcharge_peak' => env('PRICING_SURCHARGE_PEAK', 0.20), // 20%
    'per_unit_surcharge_peak_threshold' => env('PRICING_SURCHARGE_PEAK_THRESHOLD', 300), // shipments/month

    /*
    |--------------------------------------------------------------------------
    | Dimension Categories
    |--------------------------------------------------------------------------
    |
    | Size category thresholds based on L+W+H sum in cm.
    |
    */
    'dimension_categories' => [
        'micro' => ['min' => 0, 'max' => 30], // MICRO: ≤30 cm
        'mgt' => ['min' => 31, 'max' => 60], // MGT: 31-60 cm
        'sgt' => ['min' => 61, 'max' => 120], // SGT: 61-120 cm
        'kgt' => ['min' => 121, 'max' => 999999], // KGT: >120 cm
    ],

    /*
    |--------------------------------------------------------------------------
    | Rounding Configuration
    |--------------------------------------------------------------------------
    |
    | Operational fees are rounded up to the nearest multiple.
    |
    */
    'operational_fee_rounding' => 1000, // Round to nearest 1000 UZS
    'plan_fee_rounding' => 10000, // Round plan monthly fees to nearest 10000

    /*
    |--------------------------------------------------------------------------
    | Returns Storage Free Period
    |--------------------------------------------------------------------------
    |
    | Number of days returns storage is free before normal rates apply.
    |
    */
    'returns_free_storage_days' => 10,

    /*
    |--------------------------------------------------------------------------
    | Inbound Defaults
    |--------------------------------------------------------------------------
    |
    | Default items per box for inbound calculations.
    |
    */
    'inbound_box_included_items' => 50,
    
    /*
    |--------------------------------------------------------------------------
    | Plan Discount Targets (vs per-unit at target utilization)
    |--------------------------------------------------------------------------
    | target_util: percentage of included shipments to use for calculation
    | discount: how much cheaper plan should be vs per-unit equivalent
    */
    
    'plan_targets' => [
        'lite' => [
            'target_util' => 0.80,  // 80% of included shipments
            'discount' => 0.13,     // 13% cheaper than per-unit
        ],
        'start' => [
            'target_util' => 0.85,
            'discount' => 0.15,     // 15% cheaper
        ],
        'pro' => [
            'target_util' => 0.90,
            'discount' => 0.18,     // 18% cheaper
        ],
        'business' => [
            'target_util' => 0.92,
            'discount' => 0.20,     // 20% cheaper
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Default Shipment Mix (for plan fee calculations)
    |--------------------------------------------------------------------------
    | Realistic SKU mix: SGT-dominant
    */
    
    'default_shipment_mix' => [
        'micro_ratio' => 0.10,
        'mgt_ratio' => 0.25,
        'sgt_ratio' => 0.45,
        'kgt_ratio' => 0.20,
    ],
];

