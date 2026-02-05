<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\BillableOperation;
use App\Models\BillingBalance;
use App\Models\BillingBalanceTransaction;
use App\Models\BillingInvoice;
use App\Models\BillingItem;
use App\Models\BillingSubscription;
use App\Models\SellermindAccountLink;
use App\Models\ServiceAddon;
use App\Services\AddonService;
use App\Services\BillingCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BillingReportController extends Controller
{
    /**
     * Billing dashboard — summary report.
     */
    public function index(Request $request, BillingCalculator $calculator)
    {
        $company = $request->attributes->get('currentCompany');
        $companyId = $company->id;

        // Balance
        $billingBalance = BillingBalance::getOrCreate($companyId);

        // Active subscription
        $subscription = BillingSubscription::where('company_id', $companyId)
            ->where('status', 'active')
            ->with('billingPlan')
            ->first();

        // Current period estimate
        $estimate = $calculator->getCurrentPeriodEstimate($companyId);

        // Recent invoices
        $recentInvoices = BillingInvoice::where('company_id', $companyId)
            ->orderByDesc('issue_date')
            ->take(10)
            ->get();

        // Recent transactions
        $recentTransactions = BillingBalanceTransaction::where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // Chart data: operations per month (last 6 months)
        $chartData = $this->buildChartData($companyId);

        // SellerMind integration status
        $sellermindLink = SellermindAccountLink::where('company_id', $companyId)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        return view('cabinet.billing.report', compact(
            'billingBalance',
            'subscription',
            'estimate',
            'recentInvoices',
            'recentTransactions',
            'chartData',
            'sellermindLink'
        ));
    }

    /**
     * Billing invoice detail.
     */
    public function showInvoice(Request $request, BillingInvoice $billingInvoice)
    {
        $company = $request->attributes->get('currentCompany');

        if ($billingInvoice->company_id !== $company->id) {
            abort(403);
        }

        $billingInvoice->load('lines');

        return view('cabinet.billing.invoice', compact('billingInvoice'));
    }

    /**
     * Transaction history page.
     */
    public function transactions(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        $transactions = BillingBalanceTransaction::where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('cabinet.billing.transactions', compact('transactions'));
    }

    /**
     * Build chart data for the last 6 months.
     */
    private function buildChartData(int $companyId): array
    {
        $labels = [];
        $storageCosts = [];
        $shipmentCosts = [];
        $receivingCosts = [];
        $returnCosts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $start = $date->copy()->startOfMonth()->toDateString();
            $end = $date->copy()->endOfMonth()->toDateString();

            $storageCosts[] = (float) BillableOperation::where('company_id', $companyId)
                ->where('operation_type', 'storage')
                ->whereBetween('operation_date', [$start, $end])
                ->sum('total_cost');

            $shipmentCosts[] = (float) BillableOperation::where('company_id', $companyId)
                ->where('operation_type', 'shipment')
                ->whereBetween('operation_date', [$start, $end])
                ->sum('total_cost');

            $receivingCosts[] = (float) BillableOperation::where('company_id', $companyId)
                ->where('operation_type', 'receiving')
                ->whereBetween('operation_date', [$start, $end])
                ->sum('total_cost');

            $returnCosts[] = (float) BillableOperation::where('company_id', $companyId)
                ->where('operation_type', 'return')
                ->whereBetween('operation_date', [$start, $end])
                ->sum('total_cost');
        }

        return [
            'labels' => $labels,
            'storage' => $storageCosts,
            'shipments' => $shipmentCosts,
            'receiving' => $receivingCosts,
            'returns' => $returnCosts,
        ];
    }

    /**
     * Billing items (additional services charges) page.
     */
    public function charges(Request $request, AddonService $addonService)
    {
        $company = $request->attributes->get('currentCompany');

        // Period filter (default to current month)
        $period = $request->get('period', now()->format('Y-m'));

        // Scope filter
        $scopeFilter = $request->get('scope');

        // Get billing summary grouped by scope
        $summary = $addonService->getBillingSummary($company->id, $period);

        // Get detailed items with pagination
        $query = BillingItem::forCompany($company->id)
            ->forPeriod($period)
            ->orderBy('scope')
            ->orderByDesc('billed_at');

        if ($scopeFilter && $scopeFilter !== 'all') {
            $query->byScope($scopeFilter);
        }

        $items = $query->paginate(20)->appends($request->query());

        // Get available periods (last 12 months)
        $periods = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $periods[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        // Scope options for filter
        $scopeOptions = ServiceAddon::getScopeOptions();

        return view('cabinet.billing.charges', compact(
            'summary',
            'items',
            'period',
            'periods',
            'scopeFilter',
            'scopeOptions'
        ));
    }
}
