<?php

namespace App\Console\Commands;

use App\Models\BillingBalance;
use App\Models\BillingBalanceTransaction;
use App\Models\BillingItem;
use App\Models\Company;
use Illuminate\Console\Command;

class BillingSyncBalance extends Command
{
    protected $signature = 'billing:sync-balance
        {--company= : Sync only for specific company ID}
        {--period= : Period to sync (Y-m), defaults to current month}
        {--dry-run : Show what would be charged without actually charging}';

    protected $description = 'Sync BillingBalance with accrued BillingItems (backfill missing charges)';

    public function handle(): int
    {
        $companyId = $this->option('company');
        $period = $this->option('period') ?? now()->format('Y-m');
        $dryRun = $this->option('dry-run');

        $this->info("Syncing billing balance for period: {$period}" . ($dryRun ? ' [DRY RUN]' : ''));

        $query = Company::query();
        if ($companyId) {
            $query->where('id', $companyId);
        }
        $companies = $query->get();

        $totalCharged = 0;
        $companiesAffected = 0;

        foreach ($companies as $company) {
            // Sum all BillingItems accrued this period
            $itemsTotal = BillingItem::forCompany($company->id)
                ->forPeriod($period)
                ->whereIn('status', [BillingItem::STATUS_ACCRUED, BillingItem::STATUS_INVOICED])
                ->sum('amount');

            if ($itemsTotal <= 0) {
                continue;
            }

            // Sum all charges already recorded in BillingBalance for this period
            $alreadyCharged = BillingBalanceTransaction::where('company_id', $company->id)
                ->where('type', 'charge')
                ->whereYear('created_at', substr($period, 0, 4))
                ->whereMonth('created_at', substr($period, 5, 2))
                ->sum('amount'); // amount is negative for charges

            $alreadyCharged = abs((float) $alreadyCharged);

            // Apply discounts to items total to get effective amount
            $effectiveTotal = $company->applyDiscounts((float) $itemsTotal, 'overage');

            $gap = $effectiveTotal - $alreadyCharged;

            if ($gap <= 0) {
                $this->line("  {$company->name}: OK (charged={$alreadyCharged}, items={$effectiveTotal})");
                continue;
            }

            $this->info(sprintf(
                '  %s: gap=%s UZS (items=%s, charged=%s)',
                $company->name,
                number_format($gap),
                number_format($effectiveTotal),
                number_format($alreadyCharged)
            ));

            if (!$dryRun) {
                $balance = BillingBalance::getOrCreate($company->id);
                $balance->charge(
                    $gap,
                    "Синхронизация начислений за {$period}",
                    BillingItem::class,
                    null
                );
            }

            $totalCharged += $gap;
            $companiesAffected++;
        }

        $this->newLine();
        $this->info(sprintf(
            'Done. Companies affected: %d, Total%s: %s UZS',
            $companiesAffected,
            $dryRun ? ' (would charge)' : ' charged',
            number_format($totalCharged)
        ));

        return Command::SUCCESS;
    }
}
