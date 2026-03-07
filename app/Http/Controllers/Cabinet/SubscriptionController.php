<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\BillingPlan;
use App\Models\BillingSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        
        // Get user's current subscription plan (via subscription_plan_id FK)
        $currentSubscription = $currentCompany->subscription_plan_id
            ? (object) ['plan_id' => $currentCompany->subscription_plan_id]
            : null;
        
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
        if ($currentCompany->subscription_plan_id == $selectedPlan->id) {
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
     * Activate selected plan and show confirmation page
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
        $currentSubscription = $currentCompany->subscription_plan_id
            ? (object) ['plan_id' => $currentCompany->subscription_plan_id]
            : null;

        // Clear session
        session()->forget(['selected_plan_id', 'selected_plan_name']);

        // Save selection: update company's plan and create billing subscription record
        DB::transaction(function () use ($currentCompany, $selectedPlan) {
            // Update company's active subscription plan
            $currentCompany->update(['subscription_plan_id' => $selectedPlan->id]);

            // Cancel any previous active subscriptions
            BillingSubscription::where('company_id', $currentCompany->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            // Find corresponding BillingPlan by code (same codes in both tables)
            $billingPlan = BillingPlan::where('code', $selectedPlan->code)->first();

            if ($billingPlan) {
                BillingSubscription::create([
                    'company_id'      => $currentCompany->id,
                    'billing_plan_id' => $billingPlan->id,
                    'started_at'      => now(),
                    'expires_at'      => null,
                    'status'          => 'active',
                ]);
            }
        });

        return view('cabinet.subscription.confirm', compact('selectedPlan', 'currentSubscription'));
    }
}
