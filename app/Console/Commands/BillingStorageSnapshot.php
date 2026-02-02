<?php

namespace App\Console\Commands;

use App\Models\BillableOperation;
use App\Models\BillingPlan;
use App\Models\BillingSubscription;
use App\Models\Company;
use App\Models\Inventory;
use App\Models\StorageSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillingStorageSnapshot extends Command
{
    protected $signature = 'billing:storage-snapshot {--date= : Date for snapshot (Y-m-d), defaults to today}';
    protected $description = 'Take daily storage snapshot for billing calculation';

    public function handle(): int
    {
        $date = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : now();

        $dateStr = $date->toDateString();
        $this->info("Taking storage snapshot for {$dateStr}...");

        $companies = Company::whereHas('billingSubscription', function ($q) {
            $q->where('status', 'active');
        })->get();

        if ($companies->isEmpty()) {
            // Fallback: take snapshots for all companies with inventory
            $companyIds = Inventory::where('qty_total', '>', 0)
                ->distinct()
                ->pluck('company_id');

            $companies = Company::whereIn('id', $companyIds)->get();
        }

        $count = 0;
        foreach ($companies as $company) {
            $totalUnits = Inventory::where('company_id', $company->id)
                ->sum('qty_total');

            if ($totalUnits <= 0) {
                continue;
            }

            // Get storage rate from active billing plan
            $rate = $this->getStorageRate($company->id);

            $snapshot = StorageSnapshot::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'snapshot_date' => $dateStr,
                ],
                [
                    'total_units' => $totalUnits,
                    'total_pallets' => 0,
                    'total_sqm' => 0,
                    'daily_cost' => $totalUnits * $rate,
                ]
            );

            // Record as billable operation
            BillableOperation::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'operation_type' => 'storage',
                    'operation_date' => $dateStr,
                ],
                [
                    'quantity' => $totalUnits,
                    'unit_cost' => $rate,
                    'total_cost' => $totalUnits * $rate,
                ]
            );

            $count++;
        }

        $this->info("Snapshots created for {$count} companies.");
        Log::info("billing:storage-snapshot completed", ['date' => $dateStr, 'companies' => $count]);

        return Command::SUCCESS;
    }

    private function getStorageRate(int $companyId): float
    {
        $subscription = BillingSubscription::where('company_id', $companyId)
            ->where('status', 'active')
            ->with('billingPlan')
            ->first();

        if ($subscription && $subscription->billingPlan) {
            return (float) $subscription->billingPlan->storage_rate;
        }

        // Default to PayG rate
        $payg = BillingPlan::where('code', 'payg')->first();
        return $payg ? (float) $payg->storage_rate : 150;
    }
}
