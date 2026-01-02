<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $pricing = app(PricingService::class);
        $overages = $pricing->getOverageRates();
        
        // SubscriptionPlan is fetched directly in the view
        return view('pricing', compact('overages'));
    }
}
