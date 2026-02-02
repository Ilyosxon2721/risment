<?php

namespace App\Console\Commands;

use App\Models\BillingSubscription;
use App\Services\BillingCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BillingGenerateInvoices extends Command
{
    protected $signature = 'billing:generate-invoices
        {--month= : Month to generate for (Y-m), defaults to previous month}
        {--company= : Generate only for specific company ID}';

    protected $description = 'Generate monthly billing invoices (run on 1st of each month)';

    public function handle(BillingCalculator $calculator): int
    {
        $monthOption = $this->option('month');
        if ($monthOption) {
            $periodStart = Carbon::parse($monthOption . '-01')->startOfMonth();
        } else {
            $periodStart = now()->subMonth()->startOfMonth();
        }
        $periodEnd = $periodStart->copy()->endOfMonth();

        $this->info("Generating invoices for period: {$periodStart->toDateString()} to {$periodEnd->toDateString()}");

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
