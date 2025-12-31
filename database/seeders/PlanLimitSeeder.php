<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\PlanLimit;
use Illuminate\Database\Seeder;

class PlanLimitSeeder extends Seeder
{
    /**
     * Migrate existing plan limit data to plan_limits table
     */
    public function run(): void
    {
        $plans = SubscriptionPlan::all();
        
        foreach ($plans as $plan) {
            // Check if limits already exist
            if ($plan->limits()->exists()) {
                $this->command->info("Limits already exist for plan: {$plan->code}");
                continue;
            }
            
            // Create limits from existing plan fields
            PlanLimit::create([
                'plan_id' => $plan->id,
                'included_shipments' => $plan->fbs_shipments_included ?? 0,
                'included_boxes' => $plan->storage_included_boxes ?? 0,
                'included_bags' => $plan->storage_included_bags ?? 0,
                'included_inbound_boxes' => $plan->inbound_included_boxes ?? 0,
            ]);
            
            $this->command->info("Created limits for plan: {$plan->code}");
        }
        
        $this->command->info('Plan limits seeded successfully!');
    }
}
