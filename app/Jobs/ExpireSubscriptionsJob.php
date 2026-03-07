<?php

namespace App\Jobs;

use App\Models\BillingPlan;
use App\Models\BillingSubscription;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Runs daily: finds expired subscriptions and downgrades company to PAYG.
 */
class ExpireSubscriptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $paygPlan = SubscriptionPlan::where('code', 'payg')->first();
        $paygBillingPlan = BillingPlan::where('code', 'payg')->first();

        if (!$paygPlan) {
            Log::warning('ExpireSubscriptionsJob: PAYG SubscriptionPlan not found, skipping.');
            return;
        }

        // Find active subscriptions that have passed their expiry date
        $expired = BillingSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with('company')
            ->get();

        foreach ($expired as $subscription) {
            $company = $subscription->company;
            if (!$company) {
                continue;
            }

            DB::transaction(function () use ($subscription, $company, $paygPlan, $paygBillingPlan) {
                // Mark old subscription as expired
                $subscription->update(['status' => 'expired']);

                // Downgrade company to PAYG plan
                $company->update(['subscription_plan_id' => $paygPlan->id]);

                // Create PAYG subscription record (no expiry — PAYG is always active)
                if ($paygBillingPlan) {
                    BillingSubscription::create([
                        'company_id'      => $company->id,
                        'billing_plan_id' => $paygBillingPlan->id,
                        'started_at'      => now(),
                        'expires_at'      => null,
                        'status'          => 'active',
                    ]);
                }
            });

            Log::info("ExpireSubscriptionsJob: company #{$company->id} ({$company->name}) downgraded to PAYG.");
        }

        Log::info("ExpireSubscriptionsJob: processed {$expired->count()} expired subscriptions.");
    }
}
