<?php

namespace App\Console\Commands;

use App\Models\BillingInvoice;
use App\Models\BillingItem;
use App\Models\BillingSubscription;
use App\Models\Company;
use App\Services\BillingCalculator;
use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingGenerateInvoices extends Command
{
    protected $signature = 'billing:generate-invoices
        {--month= : Month to generate for (Y-m), defaults to previous month}
        {--company= : Generate only for specific company ID}
        {--use-ledger : Use new billing_items ledger instead of BillingCalculator}
        {--issue : Immediately issue the generated invoices}';

    protected $description = 'Generate monthly billing invoices (run on 1st of each month)';

    public function handle(BillingCalculator $calculator, BillingService $billingService): int
    {
        $monthOption = $this->option('month');
        if ($monthOption) {
            $periodStart = Carbon::parse($monthOption . '-01')->startOfMonth();
        } else {
            $periodStart = now()->subMonth()->startOfMonth();
        }
        $periodEnd = $periodStart->copy()->endOfMonth();
        $period = $periodStart->format('Y-m');

        $this->info("Generating invoices for period: {$periodStart->toDateString()} to {$periodEnd->toDateString()}");

        $useLedger = $this->option('use-ledger');
        $shouldIssue = $this->option('issue');

        if ($useLedger) {
            return $this->generateFromLedger($period, $shouldIssue);
        }

        return $this->generateFromCalculator($calculator, $periodStart, $periodEnd);
    }

    /**
     * Generate invoices from billing_items ledger (new system)
     */
    private function generateFromLedger(string $period, bool $shouldIssue): int
    {
        $this->info("Using new billing_items ledger...");

        // Find companies with accrued billing items
        $companyQuery = BillingItem::where('period', $period)
            ->where('status', BillingItem::STATUS_ACCRUED)
            ->select('company_id')
            ->distinct();

        if ($this->option('company')) {
            $companyQuery->where('company_id', $this->option('company'));
        }

        $companyIds = $companyQuery->pluck('company_id');

        if ($companyIds->isEmpty()) {
            $this->warn("No companies with accrued billing items for {$period}");
            return Command::SUCCESS;
        }

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($companyIds as $companyId) {
            $company = Company::find($companyId);
            if (!$company) {
                continue;
            }

            $this->line("Processing company: {$company->name} (#{$company->id})...");

            try {
                // Check if invoice already exists for this period
                $existingInvoice = BillingInvoice::where('company_id', $companyId)
                    ->where('period', $period)
                    ->whereNotIn('status', [BillingInvoice::STATUS_CANCELLED])
                    ->first();

                if ($existingInvoice) {
                    $this->line("  Skipped (invoice already exists: #{$existingInvoice->invoice_number})");
                    $skipped++;
                    continue;
                }

                // Generate invoice from billing items
                $invoice = DB::transaction(function () use ($companyId, $period) {
                    return BillingInvoice::createFromBillingItems($companyId, $period);
                });

                if ($invoice) {
                    if ($shouldIssue) {
                        $invoice->issue();
                        $this->info("  Invoice #{$invoice->invoice_number}: " .
                            number_format($invoice->total, 0, '', ' ') . ' UZS (issued)');
                    } else {
                        $this->info("  Invoice #{$invoice->invoice_number}: " .
                            number_format($invoice->total, 0, '', ' ') . ' UZS (draft)');
                    }
                    $generated++;
                } else {
                    $skipped++;
                    $this->line('  Skipped (no charges)');
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error: " . $e->getMessage());
                Log::error('Invoice generation failed (ledger)', [
                    'company_id' => $companyId,
                    'period' => $period,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Done! Generated: {$generated}, Skipped: {$skipped}, Errors: {$errors}");
        Log::info('billing:generate-invoices completed (ledger)', [
            'period' => $period,
            'generated' => $generated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Generate invoices using legacy BillingCalculator (existing system)
     */
    private function generateFromCalculator(BillingCalculator $calculator, Carbon $periodStart, Carbon $periodEnd): int
    {
        $query = BillingSubscription::where('status', 'active')
            ->with(['company', 'billingPlan']);

        if ($this->option('company')) {
            $query->where('company_id', $this->option('company'));
        }

        $subscriptions = $query->get();

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($subscriptions as $subscription) {
            $company = $subscription->company;
            $this->line("Processing company: {$company->name} (#{$company->id})...");

            try {
                $invoice = $calculator->generateInvoice($company->id, $periodStart, $periodEnd);

                if ($invoice) {
                    $generated++;
                    $this->info("  Invoice #{$invoice->invoice_number}: " .
                        number_format($invoice->total, 0, '', ' ') . ' UZS');
                } else {
                    $skipped++;
                    $this->line('  Skipped (no charges or no plan)');
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error: " . $e->getMessage());
                Log::error('Invoice generation failed', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Done! Generated: {$generated}, Skipped: {$skipped}, Errors: {$errors}");
        Log::info('billing:generate-invoices completed', [
            'period' => $periodStart->format('Y-m'),
            'generated' => $generated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
