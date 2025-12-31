<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Base Operational Rates (UZS)
    |--------------------------------------------------------------------------
    */
    
    'pick_pack_fee' => 7000, // Per order pick & pack
    
    'delivery_rates' => [
        'mgt' => 4000,  // ≤60cm
        'sgt' => 8000,  // 61-120cm  
        'kgt' => 20000, // >120cm
    ],
    
    'storage_box_monthly' => 18000, // Per 60x40x40 box per month
    'storage_bag_monthly' => 12000, // Per bag per month
    'inbound_box_fee' => 15000,     // Per inbound box
    
    /*
    |--------------------------------------------------------------------------
    | Per-Unit Surcharge Tiers (for разовый тариф)
    |--------------------------------------------------------------------------
    | Apply surcharge ONLY to FBS operational costs (pick&pack + delivery)
    | NOT to storage or inbound fees
    */
    
    'per_unit_surcharge_tiers' => [
        ['max' => 50, 'rate' => 0.00],           // 0% for very small volumes
        ['max' => 300, 'rate' => 0.10],          // 10% for medium volumes
        ['max' => PHP_INT_MAX, 'rate' => 0.20],  // 20% for large volumes
    ],
    
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
            'discount' => 0.13,     // 13% cheaper than per-unit (increased for margin)
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
        'mgt_ratio' => 0.30,
        'sgt_ratio' => 0.50,
        'kgt_ratio' => 0.20,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rounding
    |--------------------------------------------------------------------------
    */
    
    'rate_rounding' => 1000,      // Round per-unit rates to nearest 1000
    'fee_rounding' => 10000,      // Round plan monthly fees to nearest 10000
];
