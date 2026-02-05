<?php

namespace App\Console\Commands;

use App\Models\BillableOperation;
use App\Models\BillingItem;
use App\Models\BillingPlan;
use App\Models\BillingSubscription;
use App\Models\Company;
use App\Models\Inventory;
use App\Models\PricingRate;
use App\Models\StorageSnapshot;
use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillingStorageSnapshot extends Command
{
    protected $signature = 'billing:storage-snapshot
                            {--date= : Date for snapshot (Y-m-d), defaults to today}
                            {--company= : Specific company ID to process}';
    protected $description = 'Take daily storage snapshot and accrue storage charges';

    public function handle(BillingService $billingService): int
    {
        $date = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : now()->subDay(); // Accrue for yesterday by default

        $dateStr = $date->toDateString();
        $this->info("Processing storage accrual for {$dateStr}...");

        // Get companies to process
        if ($this->option('company')) {
            $companies = Company::where('id', $this->option('company'))->get();
        } else {
            // Companies with active subscriptions
            $companies = Company::whereHas('billingSubscription', function ($q) {
                $q->where('status', 'active');
            })->get();

            if ($companies->isEmpty()) {
                // Fallback: companies with inventory
                $companyIds = Inventory::where('qty_total', '>', 0)
                    ->distinct()
                    ->pluck('company_id');

                $companies = Company::whereIn('id', $companyIds)->get();
            }
        }

        $count = 0;
        $totalAmount = 0;

        foreach ($companies as $company) {
            // Calculate storage from inventory
            $storageData = $this->calculateStorageForCompany($company->id, $dateStr);

            if ($storageData['total_units'] <= 0) {
                continue;
            }

            // Get storage rates
            $boxRate = $this->getStorageRate('STORAGE_BOX_DAY', $company->id);
            $bagRate = $this->getStorageRate('STORAGE_BAG_DAY', $company->id);

            // Legacy: Create storage snapshot
            $snapshot = StorageSnapshot::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'snapshot_date' => $dateStr,
                ],
                [
                    'total_units' => $storageData['total_units'],
                    'total_pallets' => $storageData['pallets'] ?? 0,
                    'total_sqm' => 0,
                    'daily_cost' => ($storageData['boxes'] * $boxRate) + ($storageData['bags'] * $bagRate),
                ]
            );

            // Legacy: Record as billable operation
            BillableOperation::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'operation_type' => 'storage',
                    'operation_date' => $dateStr,
                ],
                [
                    'quantity' => $storageData['total_units'],
                    'unit_cost' => $boxRate,
                    'total_cost' => ($storageData['boxes'] * $boxRate) + ($storageData['bags'] * $bagRate),
                ]
            );

            // NEW: Accrue to billing_items ledger (with idempotency)
            $this->accrueStorageToBillingItems($company->id, $dateStr, $storageData, $boxRate, $bagRate);

            $dailyCost = ($storageData['boxes'] * $boxRate) + ($storageData['bags'] * $bagRate);
            $totalAmount += $dailyCost;
            $count++;

            $this->line("  Company #{$company->id}: {$storageData['boxes']} boxes + {$storageData['bags']} bags = " . number_format($dailyCost) . " UZS");
        }

        $this->info("Storage accrued for {$count} companies. Total: " . number_format($totalAmount) . " UZS");
        Log::info("billing:storage-snapshot completed", [
            'date' => $dateStr,
            'companies' => $count,
            'total_amount' => $totalAmount,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Calculate storage units for a company
     */
    private function calculateStorageForCompany(int $companyId, string $date): array
    {
        // Count inventory items (simplified: each SKU with qty > 0 = 1 box)
        $inventory = Inventory::where('company_id', $companyId)
            ->where('qty_total', '>', 0)
            ->get();

        $boxes = 0;
        $bags = 0;

        foreach ($inventory as $item) {
            // For now, treat all as boxes
            // TODO: Add storage_type field to inventory to distinguish boxes/bags/pallets
            $boxes += $item->qty_total;
        }

        return [
            'boxes' => $boxes,
            'bags' => $bags,
            'pallets' => 0,
            'total_units' => $boxes + $bags,
        ];
    }

    /**
     * Accrue storage charges to billing_items ledger
     */
    private function accrueStorageToBillingItems(int $companyId, string $date, array $storageData, int $boxRate, int $bagRate): void
    {
        // Accrue box storage
        if ($storageData['boxes'] > 0 && $boxRate > 0) {
            BillingItem::accrue(
                companyId: $companyId,
                scope: 'storage',
                titleRu: 'Хранение (коробки)',
                titleUz: 'Saqlash (qutlar)',
                unitPrice: $boxRate,
                qty: $storageData['boxes'],
                sourceType: BillingItem::SOURCE_STORAGE_DAILY,
                sourceId: null,
                addonCode: 'STORAGE_BOX_DAY',
                meta: ['date' => $date, 'boxes' => $storageData['boxes']],
                idempotencySuffix: $date . '_boxes',
                occurredAt: \Carbon\Carbon::parse($date)
            );
        }

        // Accrue bag storage
        if ($storageData['bags'] > 0 && $bagRate > 0) {
            BillingItem::accrue(
                companyId: $companyId,
                scope: 'storage',
                titleRu: 'Хранение (мешки)',
                titleUz: 'Saqlash (sumkalar)',
                unitPrice: $bagRate,
                qty: $storageData['bags'],
                sourceType: BillingItem::SOURCE_STORAGE_DAILY,
                sourceId: null,
                addonCode: 'STORAGE_BAG_DAY',
                meta: ['date' => $date, 'bags' => $storageData['bags']],
                idempotencySuffix: $date . '_bags',
                occurredAt: \Carbon\Carbon::parse($date)
            );
        }
    }

    /**
     * Get storage rate from pricing_rates or subscription
     */
    private function getStorageRate(string $rateCode, int $companyId): int
    {
        // First try to get from pricing_rates table (new system)
        $rate = PricingRate::where('code', $rateCode)
            ->where('is_active', true)
            ->value('value');

        if ($rate !== null) {
            return (int) $rate;
        }

        // Fallback to subscription plan rate
        $subscription = BillingSubscription::where('company_id', $companyId)
            ->where('status', 'active')
            ->with('billingPlan')
            ->first();

        if ($subscription && $subscription->billingPlan) {
            return (int) $subscription->billingPlan->storage_rate;
        }

        // Default fallback
        $payg = BillingPlan::where('code', 'payg')->first();
        return $payg ? (int) $payg->storage_rate : 150;
    }
}
