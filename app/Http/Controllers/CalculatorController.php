<?php

namespace App\Http\Controllers;

use App\Mail\CalculatorResultMail;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class CalculatorController extends Controller
{
    protected PricingService $pricingService;
    
    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function index(Request $request)
    {
        $history = $request->session()->get('calculator_history', []);
        
        return view('calculator', [
            'history' => array_reverse($history), // Show newest first
        ]);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'micro_count' => 'required|integer|min:0',
            'mgt_count' => 'required|integer|min:0',
            'sgt_count' => 'required|integer|min:0',
            'kgt_count' => 'required|integer|min:0',
            'storage_box_days' => 'required|integer|min:0',
            'storage_bag_days' => 'required|integer|min:0',
            'inbound_boxes' => 'required|integer|min:0',
            'avg_items_per_order' => 'nullable|numeric|min:1|max:10',
        ]);

        // Default avg_items_per_order to 1 if not provided
        $avgItemsPerOrder = $validated['avg_items_per_order'] ?? 1.0;

        // Use PricingService for all calculations
        $comparison = $this->pricingService->compareAllOptions(
            $validated['mgt_count'],
            $validated['sgt_count'],
            $validated['kgt_count'],
            $validated['storage_box_days'],
            $validated['storage_bag_days'],
            $validated['inbound_boxes'],
            $avgItemsPerOrder,
            $validated['micro_count']
        );

        $result = [
            'usage' => [
                'micro_count' => $validated['micro_count'],
                'mgt_count' => $validated['mgt_count'],
                'sgt_count' => $validated['sgt_count'],
                'kgt_count' => $validated['kgt_count'],
                'total_shipments' => $validated['micro_count'] + $validated['mgt_count'] + $validated['sgt_count'] + $validated['kgt_count'],
                'storage_box_days' => $validated['storage_box_days'],
                'storage_bag_days' => $validated['storage_bag_days'],
                'inbound_boxes' => $validated['inbound_boxes'],
                'avg_items_per_order' => $avgItemsPerOrder,
            ],
            'comparison' => $comparison,
        ];
        
        // Save to history (max 10 entries)
        $historyEntry = [
            'id' => uniqid(),
            'date' => now()->format('d.m.Y H:i'),
            'inputs' => $validated,
            'best_plan' => $comparison['recommended']['plan'] ?? null,
            'best_price' => $comparison['recommended']['total'] ?? 0,
        ];
        
        $history = $request->session()->get('calculator_history', []);
        $history[] = $historyEntry;
        
        // Keep only last 10 entries
        if (count($history) > 10) {
            $history = array_slice($history, -10);
        }
        
        $request->session()->put('calculator_history', $history);
        
        return view('calculator', [
            'result' => $result,
            'history' => array_reverse($history),
        ]);
    }
    
    public function sendResults(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
            'micro_count' => 'required|integer|min:0',
            'mgt_count' => 'required|integer|min:0',
            'sgt_count' => 'required|integer|min:0',
            'kgt_count' => 'required|integer|min:0',
            'storage_box_days' => 'required|integer|min:0',
            'storage_bag_days' => 'required|integer|min:0',
            'inbound_boxes' => 'required|integer|min:0',
            'avg_items_per_order' => 'nullable|numeric|min:1|max:10',
        ]);

        // Rate limiting: 3 emails per IP per 10 minutes
        $rateLimitKey = 'calculator-email:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()
                ->withInput()
                ->with('email_error', __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]));
        }
        RateLimiter::hit($rateLimitKey, 600);

        $avgItemsPerOrder = $request->input('avg_items_per_order', 1.0);

        $comparison = $this->pricingService->compareAllOptions(
            $request->input('mgt_count'),
            $request->input('sgt_count'),
            $request->input('kgt_count'),
            $request->input('storage_box_days'),
            $request->input('storage_bag_days'),
            $request->input('inbound_boxes'),
            $avgItemsPerOrder,
            $request->input('micro_count')
        );

        $result = [
            'usage' => [
                'micro_count' => (int) $request->input('micro_count'),
                'mgt_count' => (int) $request->input('mgt_count'),
                'sgt_count' => (int) $request->input('sgt_count'),
                'kgt_count' => (int) $request->input('kgt_count'),
                'total_shipments' => (int) $request->input('micro_count') + (int) $request->input('mgt_count') + (int) $request->input('sgt_count') + (int) $request->input('kgt_count'),
                'storage_box_days' => (int) $request->input('storage_box_days'),
                'storage_bag_days' => (int) $request->input('storage_bag_days'),
                'inbound_boxes' => (int) $request->input('inbound_boxes'),
                'avg_items_per_order' => $avgItemsPerOrder,
            ],
            'comparison' => $comparison,
        ];

        Mail::to($request->input('email'))->send(new CalculatorResultMail($result));

        return back()
            ->withInput()
            ->with('email_sent', __('Calculator results have been sent to your email.'));
    }

    public function clearHistory(Request $request)
    {
        $request->session()->forget('calculator_history');

        return redirect()->route('calculator', ['locale' => app()->getLocale()])
            ->with('success', __('Calculator history cleared'));
    }
}
