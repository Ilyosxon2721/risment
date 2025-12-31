<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display all available subscription packages
     */
    public function choose(Request $request)
    {
        $currentCompany = $request->attributes->get('currentCompany');
        
        // Get all subscription plans ordered by price
        $plans = SubscriptionPlan::orderBy('price_month')->get();
        
        // Get user's current subscription if exists
        $currentSubscription = $currentCompany->subscription;
        
        return view('cabinet.subscription.choose', compact('plans', 'currentSubscription'));
    }
    
    /**
     * Handle package selection
     */
    public function select(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        $currentCompany = $request->attributes->get('currentCompany');
        $selectedPlan = SubscriptionPlan::findOrFail($request->plan_id);
        
        // Check if user is selecting their current plan
        if ($currentCompany->subscription && $currentCompany->subscription->plan_id == $selectedPlan->id) {
            return redirect()
                ->route('cabinet.dashboard')
                ->with('info', __('You are already subscribed to this plan'));
        }
        
        // Store selection in session for confirmation
        session([
            'selected_plan_id' => $selectedPlan->id,
            'selected_plan_name' => $selectedPlan->getName(),
        ]);
        
        return redirect()->route('cabinet.subscription.confirm');
    }
    
    /**
     * Show confirmation page after package selection
     */
    public function confirm(Request $request)
    {
        $currentCompany = $request->attributes->get('currentCompany');
        
        // Get selected plan from session
        $planId = session('selected_plan_id');
        
        if (!$planId) {
            return redirect()
                ->route('cabinet.subscription.choose')
                ->with('error', __('Please select a plan first'));
        }
        
        $selectedPlan = SubscriptionPlan::findOrFail($planId);
        $currentSubscription = $currentCompany->subscription;
        
        // Clear session
        session()->forget(['selected_plan_id', 'selected_plan_name']);
        
        return view('cabinet.subscription.confirm', compact('selectedPlan', 'currentSubscription'));
    }
}
