<?php

use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\SellermindWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', 'throttle:60,1'])->group(function () {
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store']);
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy']);
});

// SellerMind webhooks (token-based auth, no session required)
Route::post('/integration/sellermind/confirm', [SellermindWebhookController::class, 'confirmLink'])
    ->middleware('throttle:10,1');
Route::post('/integration/sellermind/disconnect', [SellermindWebhookController::class, 'disconnectLink'])
    ->middleware('throttle:10,1');
