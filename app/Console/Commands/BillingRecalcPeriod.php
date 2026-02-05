<?php

namespace App\Console\Commands;

use App\Models\BillingItem;
use App\Models\Company;
use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingRecalcPeriod extends Command
{
    protected $signature = 'billing:recalc-period
        {company : Company ID to recalculate}
        {--period= : Period to recalculate (Y-m), defaults to current month}
        {--dry-run : Show what would be done without making changes}
        {--void-existing : Void existing accrued items before recalculating}';

    protected $description = 'Recalculate billing items for a specific company and period';

    public function handle(BillingService $billingService): int
    {
        $companyId = $this->argument('company');
        $period = $this->option('period') ?? now()->format('Y-m');
        $dryRun = $this->option('dry-run');
        $voidExisting = $this->option('void-existing');

        $company = Company::find($companyId);
        if (!$company) {
            $this->error("Company #{$companyId} not found.");
            return Command::FAILURE;
        }

        $this->info("Recalculating billing for: {$company->name} (#{$company->id})");
        $this->info("Period: {$period}");

        if ($dryRun) {
            $this->warn("DRY RUN - No changes will be made.");
        }

        // Get current accrued items
        $existingItems = BillingItem::forCompany($companyId)
            ->forPeriod($period)
            ->where('status', BillingItem::STATUS_ACCRUED)
            ->get();

        $this->newLine();
        $this->info("Current accrued items: {$existingItems->count()}");

        if ($existingItems->count() > 0) {
            $this->table(
                ['ID', 'Scope', 'Title', 'Qty', 'Unit Price', 'Amount', 'Addon Code'],
                $existingItems->map(fn ($item) => [
                    $item->id,
                    $item->scope,
                    \Illuminate\Support\Str::limit($item->title_ru, 30),
                    $item->qty,
                    number_format($item->unit_price),
                    number_format($item->amount),
                    $item->addon_code,
                ])->toArray()
            );

            $currentTotal = $existingItems->sum('amount');
            $this->info("Current total: " . number_format($currentTotal) . " UZS");
        }

        if ($dryRun) {
            return Command::SUCCESS;
        }

        // Void existing items if requested
        if ($voidExisting && $existingItems->count() > 0) {
            $this->newLine();
            $this->warn("Voiding {$existingItems->count()} existing items...");

            DB::transaction(function () use ($existingItems) {
                foreach ($existingItems as $item) {
                    $item->void();
                }
            });

            $this->info("Voided {$existingItems->count()} items.");
        }

        // Recalculate storage for each day in the period
        $this->newLine();
        $this->info("Recalculating storage charges...");

        $periodStart = Carbon::parse($period . '-01');
        $periodEnd = $periodStart->copy()->endOfMonth();
        $today = now()->toDateString();
        $storageItemsCreated = 0;

        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            // Don't calculate future dates
            if ($date->toDateString() > $today) {
                break;
            }

            $items = $billingService->accrueStorageDaily($companyId, $date->toDateString());
            $storageItemsCreated += count($items);
        }

        $this->info("Storage charges recalculated: {$storageItemsCreated} items created");

        // Show new totals
        $newItems = BillingItem::forCompany($companyId)
            ->forPeriod($period)
            ->where('status', BillingItem::STATUS_ACCRUED)
            ->get();

        $newTotal = $newItems->sum('amount');
        $this->newLine();
        $this->info("New totals:");
        $this->info("  Items: {$newItems->count()}");
        $this->info("  Total: " . number_format($newTotal) . " UZS");

        Log::info('billing:recalc-period completed', [
            'company_id' => $companyId,
            'period' => $period,
            'voided' => $voidExisting ? $existingItems->count() : 0,
            'new_items' => $newItems->count(),
            'new_total' => $newTotal,
        ]);

        return Command::SUCCESS;
    }
}
