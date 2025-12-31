<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Payment\ClickService;
use App\Services\Payment\PaymeService;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    public function __construct(
        private ClickService $clickService,
        private PaymeService $paymeService
    ) {}

    /**
     * Show payment method selection page
     */
    public function pay(Request $request, Invoice $invoice)
    {
        $company = $request->attributes->get('currentCompany');
        
        // Check if invoice belongs to company
        if ($invoice->company_id !== $company->id) {
            abort(403);
        }
        
        // Check if already paid
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('cabinet.finance.invoices.show', $invoice)
                ->with('info', __('This invoice is already paid'));
        }
        
        return view('cabinet.finance.invoices.pay', compact('invoice'));
    }

    /**
     * Initiate Click payment
     */
    public function initiateClick(Request $request, Invoice $invoice)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($invoice->company_id !== $company->id) {
            abort(403);
        }
        
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('cabinet.finance.invoices.show', $invoice)
                ->with('info', __('This invoice is already paid'));
        }
        
        // Generate Click payment URL
        $paymentUrl = $this->clickService->generatePaymentUrl(
            $invoice->id,
            $invoice->total
        );
        
        return redirect($paymentUrl);
    }

    /**
     * Initiate Payme payment
     */
    public function initiatePayme(Request $request, Invoice $invoice)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($invoice->company_id !== $company->id) {
            abort(403);
        }
        
        if ($invoice->status === 'paid') {
            return redirect()
                ->route('cabinet.finance.invoices.show', $invoice)
                ->with('info', __('This invoice is already paid'));
        }
        
        // Generate Payme payment URL
        $paymentUrl = $this->paymeService->generatePaymentUrl(
            $invoice->id,
            $invoice->total
        );
        
        return redirect($paymentUrl);
    }
}
