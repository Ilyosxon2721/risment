<?php

namespace App\Console\Commands;

use App\Jobs\DailyStorageChargeJob;
use App\Models\BillingInvoice;
use App\Models\BillingItem;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingCloseMonth extends Command
{
    protected $signature = 'billing:close-month
        {--period= : Period to close (Y-m), defaults to previous month}
        {--company= : Close only for specific company ID}
        {--skip-storage : Skip final storage accrual}
        {--issue : Immediately issue generated invoices}
        {--force : Force close even if period is current month}';

    protected $description = 'Close a billing period: finalize storage, generate invoices, mark items as invoiced';

    public function handle(): int
    {
        $periodOption = $this->option('period');
        $companyId = $this->option('company');
        $skipStorage = $this->option('skip-storage');
        $shouldIssue = $this->option('issue');
        $force = $this->option('force');

        // Determine period
        if ($periodOption) {
            $period = $periodOption;
            $periodStart = Carbon::parse($period . '-01');
        } else {
            $periodStart = now()->subMonth()->startOfMonth();
            $period = $periodStart->format('Y-m');
        }
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Safety check: don't close current month without force
        if ($period === now()->format('Y-m') && !$force) {
            $this->error("Cannot close current month without --force flag.");
            $this->error("Use --period={$period} --force to override.");
            return Command::FAILURE;
        }

        $this->info("Closing billing period: {$period}");
        $this->info("Period dates: {$periodStart->toDateString()} to {$periodEnd->toDateString()}");
        $this->newLine();

        // Step 1: Finalize storage charges for all days in the period
        if (!$skipStorage) {
            $this->step1_finalizeStorage($companyId, $periodStart, $periodEnd);
        } else {
            $this->warn("Skipping storage finalization (--skip-storage)");
        }

        // Step 2: Generate invoices from ledger
        $this->step2_generateInvoices($period, $companyId, $shouldIssue);

        // Step 3: Summary
        $this->newLine();
        $this->showPeriodSummary($period, $companyId);

        Log::info('billing:close-month completed', [
            'period' => $period,
            'company_id' => $companyId,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Step 1: Run storage accrual for any missing days in the period
     */
    private function step1_finalizeStorage(?int $companyId, Carbon $periodStart, Carbon $periodEnd): void
    {
        $this->info("Step 1: Finalizing storage charges...");

        $today = now()->toDateString();
        $daysProcessed = 0;
        $errors = 0;

        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            // Don't process future dates
            if ($date->toDateString() > $today) {
                break;
            }

            try {
                // Run storage job synchronously
                $job = new DailyStorageChargeJob($date->toDateString(), $companyId);
                $job->handle();
                $daysProcessed++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error for {$date->toDateString()}: " . $e->getMessage());
            }
        }

        $this->info("  Processed {$daysProcessed} days" . ($errors > 0 ? ", {$errors} errors" : ""));
    }

    /**
     * Step 2: Generate invoices from billing_items ledger
     */
    private function step2_generateInvoices(string $period, ?int $companyId, bool $shouldIssue): void
    {
        $this->newLine();
        $this->info("Step 2: Generating invoices from ledger...");

        // Find companies with accrued items
        $query = BillingItem::where('period', $period)
            ->where('status', BillingItem::STATUS_ACCRUED)
            ->select('company_id')
            ->distinct();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $companyIds = $query->pluck('company_id');

        if ($companyIds->isEmpty()) {
            $this->warn("  No companies with accrued items for {$period}");
            return;
        }

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($companyIds as $cId) {
            $company = Company::find($cId);
            if (!$company) {
                continue;
            }

            try {
                // Check for existing invoice
                $existing = BillingInvoice::where('company_id', $cId)
                    ->where('period', $period)
                    ->whereNotIn('status', [BillingInvoice::STATUS_CANCELLED])
                    ->first();

                if ($existing) {
                    $this->line("  {$company->name}: Skipped (invoice exists #{$existing->invoice_number})");
                    $skipped++;
                    continue;
                }

                // Generate invoice
                $invoice = DB::transaction(function () use ($cId, $period) {
                    return BillingInvoice::createFromBillingItems($cId, $period);
                });

                if (!$invoice) {
                    $skipped++;
                    continue;
                }

                // Issue if requested
                if ($shouldIssue) {
                    $invoice->issue();
                }

                $status = $shouldIssue ? 'issued' : 'draft';
                $this->info("  {$company->name}: #{$invoice->invoice_number} - " .
                    number_format($invoice->total) . " UZS ({$status})");
                $generated++;

            } catch (\Exception $e) {
                $errors++;
                $this->error("  {$company->name}: Error - " . $e->getMessage());
                Log::error('billing:close-month invoice error', [
                    'company_id' => $cId,
                    'period' => $period,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("  Generated: {$generated}, Skipped: {$skipped}, Errors: {$errors}");
    }

    /**
     * Show summary for the closed period
     */
    private function showPeriodSummary(string $period, ?int $companyId): void
    {
        $this->info("Summary for {$period}:");

        // Count billing items
        $itemQuery = BillingItem::where('period', $period);
        if ($companyId) {
            $itemQuery->where('company_id', $companyId);
        }

        $accrued = (clone $itemQuery)->where('status', BillingItem::STATUS_ACCRUED)->count();
        $invoiced = (clone $itemQuery)->where('status', BillingItem::STATUS_INVOICED)->count();
        $void = (clone $itemQuery)->where('status', BillingItem::STATUS_VOID)->count();

        // Count invoices
        $invoiceQuery = BillingInvoice::where('period', $period);
        if ($companyId) {
            $invoiceQuery->where('company_id', $companyId);
        }

        $invoiceCount = $invoiceQuery->count();
        $totalAmount = $invoiceQuery->sum('total');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Billing items (accrued)', $accrued],
                ['Billing items (invoiced)', $invoiced],
                ['Billing items (void)', $void],
                ['Invoices generated', $invoiceCount],
                ['Total invoiced amount', number_format($totalAmount) . ' UZS'],
            ]
        );

        if ($accrued > 0) {
            $this->warn("Warning: {$accrued} items still in 'accrued' status");
        }
    }
}
