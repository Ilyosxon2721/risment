<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Click and Payme payment gateways
    |
    */

    'click' => [
        'merchant_id' => env('CLICK_MERCHANT_ID'),
        'service_id' => env('CLICK_SERVICE_ID'),
        'secret_key' => env('CLICK_SECRET_KEY'),
        'merchant_user_id' => env('CLICK_MERCHANT_USER_ID'),
    ],

    'payme' => [
        'merchant_id' => env('PAYME_MERCHANT_ID'),
        'secret_key' => env('PAYME_SECRET_KEY'),
        'endpoint' => env('PAYME_ENDPOINT', 'https://checkout.paycom.uz'),
    ],

];
