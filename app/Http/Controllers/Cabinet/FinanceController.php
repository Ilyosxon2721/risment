<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Finance dashboard - overview
     */
    public function index(Request $request)
    {
        $currentCompany = $request->attributes->get('currentCompany');
        
        // Get stats
        $currentBalance = $currentCompany->balance;
        $totalPaidThisYear = $currentCompany->payments()
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        
        $outstandingAmount = $currentCompany->invoices()
            ->whereIn('status', ['sent', 'overdue'])
            ->sum(DB::raw('total - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id)'));
        
        // Recent invoices
        $recentInvoices = $currentCompany->invoices()
            ->latest()
            ->take(5)
            ->get();
        
        // Recent payments
        $recentPayments = $currentCompany->payments()
            ->with('invoice')
            ->latest('payment_date')
            ->take(5)
            ->get();
        
        $lastPaymentDate = $currentCompany->payments()
            ->latest('payment_date')
            ->value('payment_date');
        
        return view('cabinet.finance.index', compact(
            'currentBalance',
            'totalPaidThisYear',
            'outstandingAmount',
            'lastPaymentDate',
            'recentInvoices',
            'recentPayments'
        ));
    }
    
    /**
     * All invoices list
     */
    public function invoices(Request $request)
    {
        $currentCompany = $request->attributes->get('currentCompany');
        
        $query = $currentCompany->invoices()->latest('issue_date');
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $invoices = $query->paginate(15);
        
        return view('cabinet.finance.invoices', compact('invoices'));
    }
    
    /**
     * Single invoice detail
     */
    public function show(Request $request, Invoice $invoice)
    {
        $currentCompany = $request->attributes->get('currentCompany');
        
        // Security: ensure invoice belongs to current company
        if ($invoice->company_id !== $currentCompany->id) {
            abort(403);
        }
        
        $invoice->load(['items', 'payments']);
        
        return view('cabinet.finance.invoice', compact('invoice'));
    }
    
    /**
     * All payments list
     */
    public function payments(Request $request)
    {
        $currentCompany = $request->attributes->get('currentCompany');
        
        $payments = $currentCompany->payments()
            ->with('invoice')
            ->latest('payment_date')
            ->paginate(15);
        
        return view('cabinet.finance.payments', compact('payments'));
    }
}
