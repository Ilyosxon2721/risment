<?php

namespace App\Http\Controllers;

use App\Models\TariffPlan;
use App\Services\PricingService; // Added this line
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $pricing = app(PricingService::class);
        $overages = $pricing->getOverageRates();
        
        $plan = TariffPlan::where('is_default', true)
            ->where('is_active', true)
            ->with(['items' => function($q) {
                $q->where('is_active', true)->orderBy('sort');
            }])
            ->firstOrFail();
        
        return view('pricing', compact('plan', 'overages'));
    }
}
