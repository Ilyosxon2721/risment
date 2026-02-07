<?php

namespace App\Jobs;

use App\Models\BillingItem;
use App\Models\Company;
use App\Models\Inventory;
use App\Models\PricingRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyStorageChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $date;
    protected ?int $companyId;

    /**
     * Days returns are stored for free
     */
    public const FREE_RETURN_STORAGE_DAYS = 10;

    public function __construct(?string $date = null, ?int $companyId = null)
    {
        // Default to yesterday (accrue for the previous day)
        $this->date = $date ?? now()->subDay()->toDateString();
        $this->companyId = $companyId;
    }

    public function handle(): void
    {
        Log::info('DailyStorageChargeJob started', ['date' => $this->date]);

        $companies = $this->getCompaniesToProcess();
        $totalAccrued = 0;
        $companiesProcessed = 0;

        $boxRate = $this->getRate('STORAGE_BOX_DAY');
        $bagRate = $this->getRate('STORAGE_BAG_DAY');

        foreach ($companies as $company) {
            $storage = $this->calculateStorageForCompany($company->id);

            if ($storage['total_units'] <= 0) {
                continue;
            }

            // Accrue box storage
            if ($storage['boxes'] > 0 && $boxRate > 0) {
                $item = $this->accrueStorageItem(
                    $company->id,
                    'Хранение (коробки)',
                    'Saqlash (qutlar)',
                    $boxRate,
                    $storage['boxes'],
                    'STORAGE_BOX_DAY',
                    '_boxes',
                    ['boxes' => $storage['boxes']]
                );

                if ($item) {
                    $totalAccrued += $item->amount;
                }
            }

            // Accrue bag storage
            if ($storage['bags'] > 0 && $bagRate > 0) {
                $item = $this->accrueStorageItem(
                    $company->id,
                    'Хранение (мешки)',
                    'Saqlash (xaltalar)',
                    $bagRate,
                    $storage['bags'],
                    'STORAGE_BAG_DAY',
                    '_bags',
                    ['bags' => $storage['bags']]
                );

                if ($item) {
                    $totalAccrued += $item->amount;
                }
            }

            $companiesProcessed++;

            Log::debug('Storage accrued for company', [
                'company_id' => $company->id,
                'boxes' => $storage['boxes'],
                'bags' => $storage['bags'],
                'date' => $this->date,
            ]);
        }

        Log::info('DailyStorageChargeJob completed', [
            'date' => $this->date,
            'companies_processed' => $companiesProcessed,
            'total_accrued' => $totalAccrued,
        ]);
    }

    /**
     * Get companies to process
     */
    protected function getCompaniesToProcess()
    {
        if ($this->companyId) {
            return Company::where('id', $this->companyId)->get();
        }

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

        return $companies;
    }

    /**
     * Calculate storage units for a company
     * Excludes returns within free storage period
     */
    protected function calculateStorageForCompany(int $companyId): array
    {
        $boxes = 0;
        $bags = 0;

        // Get regular inventory (boxes/bags)
        $inventory = Inventory::where('company_id', $companyId)
            ->where('qty_total', '>', 0)
            ->get();

        foreach ($inventory as $item) {
            // TODO: Add storage_type field to inventory
            // For now, treat all as boxes
            $storageType = $item->storage_type ?? 'box';

            if ($storageType === 'bag') {
                $bags += $item->qty_total;
            } else {
                $boxes += $item->qty_total;
            }
        }

        // Exclude returns within free storage period (10 days)
        $freeReturnsCount = $this->countFreeStorageReturns($companyId);
        $boxes = max(0, $boxes - $freeReturnsCount);

        return [
            'boxes' => $boxes,
            'bags' => $bags,
            'total_units' => $boxes + $bags,
        ];
    }

    /**
     * Count returns still within free storage period
     * Returns are free for first 10 days
     */
    protected function countFreeStorageReturns(int $companyId): int
    {
        $freeUntilDate = now()->subDays(self::FREE_RETURN_STORAGE_DAYS);

        // Count return items received within last 10 days
        // that haven't been picked up or processed yet
        $count = 0;

        // Check if ReturnItem model exists
        if (class_exists(\App\Models\ReturnItem::class)) {
            $count = \App\Models\ReturnItem::where('company_id', $companyId)
                ->where('received_at', '>=', $freeUntilDate)
                ->whereNull('processed_at')
                ->sum('qty');
        }

        return (int) $count;
    }

    /**
     * Accrue a storage billing item with idempotency
     */
    protected function accrueStorageItem(
        int $companyId,
        string $titleRu,
        string $titleUz,
        int $unitPrice,
        int $qty,
        string $addonCode,
        string $suffix,
        array $extraMeta = []
    ): ?BillingItem {
        return BillingItem::accrue(
            companyId: $companyId,
            scope: 'storage',
            titleRu: $titleRu,
            titleUz: $titleUz,
            unitPrice: $unitPrice,
            qty: $qty,
            sourceType: BillingItem::SOURCE_STORAGE_DAILY,
            sourceId: null,
            addonCode: $addonCode,
            meta: array_merge(['date' => $this->date], $extraMeta),
            idempotencySuffix: $this->date . $suffix,
            occurredAt: \Carbon\Carbon::parse($this->date)
        );
    }

    /**
     * Get rate from pricing_rates table
     */
    protected function getRate(string $code): int
    {
        return (int) (PricingRate::where('code', $code)
            ->where('is_active', true)
            ->value('value') ?? 0);
    }
}
