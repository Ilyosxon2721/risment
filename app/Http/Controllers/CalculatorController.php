<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;

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
            $avgItemsPerOrder
        );
        
        $result = [
            'usage' => [
                'mgt_count' => $validated['mgt_count'],
                'sgt_count' => $validated['sgt_count'],
                'kgt_count' => $validated['kgt_count'],
                'total_shipments' => $validated['mgt_count'] + $validated['sgt_count'] + $validated['kgt_count'],
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
    
    public function clearHistory(Request $request)
    {
        $request->session()->forget('calculator_history');
        
        return redirect()->route('calculator', ['locale' => app()->getLocale()])
            ->with('success', __('Calculator history cleared'));
    }
}
