<?php

namespace App\Services;

use App\Models\BillableOperation;
use App\Models\BillingBalance;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceLine;
use App\Models\BillingPlan;
use App\Models\BillingSubscription;
use App\Models\StorageSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BillingCalculator
{
    /**
     * Calculate billing for a company for a given period.
     */
    public function calculateForPeriod(int $companyId, Carbon $periodStart, Carbon $periodEnd): array
    {
        $plan = $this->getActivePlan($companyId);
        if (!$plan) {
            return ['error' => 'No active billing plan'];
        }

        $storage = $this->calculateStorage($companyId, $periodStart, $periodEnd, $plan);
        $shipments = $this->calculateShipments($companyId, $periodStart, $periodEnd, $plan);
        $receiving = $this->calculateReceiving($companyId, $periodStart, $periodEnd, $plan);
        $returns = $this->calculateReturns($companyId, $periodStart, $periodEnd, $plan);
        $subscription = $this->calculateSubscription($plan);

        $subtotal = $subscription['total'] + $storage['total'] + $shipments['total']
                  + $receiving['total'] + $returns['total'];

        return [
            'plan' => $plan,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'subscription' => $subscription,
            'storage' => $storage,
            'shipments' => $shipments,
            'receiving' => $receiving,
            'returns' => $returns,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ];
    }

    /**
     * Get the active billing plan for a company.
     */
    public function getActivePlan(int $companyId): ?BillingPlan
    {
        $subscription = BillingSubscription::where('company_id', $companyId)
            ->where('status', 'active')
            ->with('billingPlan')
            ->first();

        return $subscription?->billingPlan;
    }

    /**
     * Calculate storage cost: unit_days * storage_rate.
     */
    private function calculateStorage(int $companyId, Carbon $start, Carbon $end, BillingPlan $plan): array
    {
        $snapshots = StorageSnapshot::where('company_id', $companyId)
            ->whereBetween('snapshot_date', [$start, $end])
            ->get();

        $totalUnitDays = $snapshots->sum('total_units');
        $billableUnitDays = max(0, $totalUnitDays - $plan->included_storage_units);
        $total = $billableUnitDays * (float) $plan->storage_rate;

        return [
            'total_unit_days' => $totalUnitDays,
            'included' => $plan->included_storage_units,
            'billable_unit_days' => $billableUnitDays,
            'rate' => (float) $plan->storage_rate,
            'total' => $total,
        ];
    }

    /**
     * Calculate shipment costs: count * shipment_rate.
     */
    private function calculateShipments(int $companyId, Carbon $start, Carbon $end, BillingPlan $plan): array
    {
        $ops = BillableOperation::where('company_id', $companyId)
            ->where('operation_type', 'shipment')
            ->whereBetween('operation_date', [$start, $end])
            ->get();

        $totalCount = $ops->sum('quantity');
        $billableCount = max(0, $totalCount - $plan->included_shipments);
        $total = $billableCount * (float) $plan->shipment_rate;

        return [
            'total_count' => $totalCount,
            'included' => $plan->included_shipments,
            'billable_count' => $billableCount,
            'rate' => (float) $plan->shipment_rate,
            'total' => $total,
        ];
    }

    /**
     * Calculate receiving costs: units * receiving_rate.
     */
    private function calculateReceiving(int $companyId, Carbon $start, Carbon $end, BillingPlan $plan): array
    {
        $ops = BillableOperation::where('company_id', $companyId)
            ->where('operation_type', 'receiving')
            ->whereBetween('operation_date', [$start, $end])
            ->get();

        $totalUnits = $ops->sum('quantity');
        $billableUnits = max(0, $totalUnits - $plan->included_receiving_units);
        $total = $billableUnits * (float) $plan->receiving_rate;

        return [
            'total_units' => $totalUnits,
            'included' => $plan->included_receiving_units,
            'billable_units' => $billableUnits,
            'rate' => (float) $plan->receiving_rate,
            'total' => $total,
        ];
    }

    /**
     * Calculate return costs: units * return_rate (if not included in plan).
     */
    private function calculateReturns(int $companyId, Carbon $start, Carbon $end, BillingPlan $plan): array
    {
        if ($plan->returns_included) {
            return [
                'total_units' => 0,
                'billable_units' => 0,
                'rate' => 0,
                'total' => 0,
                'included_in_plan' => true,
            ];
        }

        $ops = BillableOperation::where('company_id', $companyId)
            ->where('operation_type', 'return')
            ->whereBetween('operation_date', [$start, $end])
            ->get();

        $totalUnits = $ops->sum('quantity');
        $total = $totalUnits * (float) $plan->return_rate;

        return [
            'total_units' => $totalUnits,
            'billable_units' => $totalUnits,
            'rate' => (float) $plan->return_rate,
            'total' => $total,
            'included_in_plan' => false,
        ];
    }

    /**
     * Calculate subscription fee.
     */
    private function calculateSubscription(BillingPlan $plan): array
    {
        return [
            'plan_name' => $plan->getName(),
            'total' => (float) $plan->monthly_fee,
        ];
    }

    /**
     * Generate an invoice for a company for a given period.
     */
    public function generateInvoice(int $companyId, Carbon $periodStart, Carbon $periodEnd): ?BillingInvoice
    {
        $calc = $this->calculateForPeriod($companyId, $periodStart, $periodEnd);

        if (isset($calc['error'])) {
            return null;
        }

        if ($calc['total'] <= 0) {
            return null;
        }

        return DB::transaction(function () use ($companyId, $periodStart, $periodEnd, $calc) {
            $invoice = BillingInvoice::create([
                'company_id' => $companyId,
                'invoice_number' => BillingInvoice::generateNumber($companyId),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'subtotal' => $calc['subtotal'],
                'tax' => $calc['tax'],
                'total' => $calc['total'],
                'status' => 'issued',
                'issue_date' => now(),
                'due_date' => now()->addDays(15),
            ]);

            // Subscription line
            if ($calc['subscription']['total'] > 0) {
                BillingInvoiceLine::create([
                    'billing_invoice_id' => $invoice->id,
                    'service_type' => 'subscription',
                    'description' => __('Monthly subscription') . ': ' . $calc['plan']->getName(),
                    'quantity' => 1,
                    'unit_price' => $calc['subscription']['total'],
                    'total_price' => $calc['subscription']['total'],
                ]);
            }

            // Storage line
            if ($calc['storage']['total'] > 0) {
                BillingInvoiceLine::create([
                    'billing_invoice_id' => $invoice->id,
                    'service_type' => 'storage',
                    'description' => __('Storage') . ': ' . $calc['storage']['billable_unit_days'] . ' ' . __('unit-days'),
                    'quantity' => $calc['storage']['billable_unit_days'],
                    'unit_price' => $calc['storage']['rate'],
                    'total_price' => $calc['storage']['total'],
                ]);
            }

            // Shipments line
            if ($calc['shipments']['total'] > 0) {
                BillingInvoiceLine::create([
                    'billing_invoice_id' => $invoice->id,
                    'service_type' => 'shipment',
                    'description' => __('FBS Shipments') . ': ' . $calc['shipments']['billable_count'] . ' ' . __('orders'),
                    'quantity' => $calc['shipments']['billable_count'],
                    'unit_price' => $calc['shipments']['rate'],
                    'total_price' => $calc['shipments']['total'],
                ]);
            }

            // Receiving line
            if ($calc['receiving']['total'] > 0) {
                BillingInvoiceLine::create([
                    'billing_invoice_id' => $invoice->id,
                    'service_type' => 'receiving',
                    'description' => __('Receiving') . ': ' . $calc['receiving']['billable_units'] . ' ' . __('units'),
                    'quantity' => $calc['receiving']['billable_units'],
                    'unit_price' => $calc['receiving']['rate'],
                    'total_price' => $calc['receiving']['total'],
                ]);
            }

            // Returns line
            if (($calc['returns']['total'] ?? 0) > 0) {
                BillingInvoiceLine::create([
                    'billing_invoice_id' => $invoice->id,
                    'service_type' => 'return',
                    'description' => __('Returns') . ': ' . $calc['returns']['billable_units'] . ' ' . __('units'),
                    'quantity' => $calc['returns']['billable_units'],
                    'unit_price' => $calc['returns']['rate'],
                    'total_price' => $calc['returns']['total'],
                ]);
            }

            // Mark operations as billed
            BillableOperation::where('company_id', $companyId)
                ->where('billed', false)
                ->whereBetween('operation_date', [$periodStart, $periodEnd])
                ->update([
                    'billed' => true,
                    'billing_invoice_id' => $invoice->id,
                ]);

            // Charge the balance
            $balance = BillingBalance::getOrCreate($companyId);
            $balance->charge($calc['total'], "Invoice {$invoice->invoice_number}", BillingInvoice::class, $invoice->id);

            return $invoice;
        });
    }

    /**
     * Get current period estimates (for dashboard display).
     */
    public function getCurrentPeriodEstimate(int $companyId): ?array
    {
        $start = now()->startOfMonth();
        $end = now();

        $calc = $this->calculateForPeriod($companyId, $start, $end);
        if (isset($calc['error'])) {
            return null;
        }

        // Project full month estimate
        $daysPassed = max(1, now()->day);
        $daysInMonth = now()->daysInMonth;
        $projectionFactor = $daysInMonth / $daysPassed;

        $calc['projected_total'] = $calc['subscription']['total']
            + ($calc['storage']['total'] * $projectionFactor)
            + ($calc['shipments']['total'] * $projectionFactor)
            + ($calc['receiving']['total'] * $projectionFactor)
            + ($calc['returns']['total'] * $projectionFactor);

        return $calc;
    }
}
