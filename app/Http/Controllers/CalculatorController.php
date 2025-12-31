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

    public function index()
    {
        return view('calculator');
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'mgt_count' => 'required|integer|min:0',
            'sgt_count' => 'required|integer|min:0',
            'kgt_count' => 'required|integer|min:0',
            'storage_boxes' => 'required|integer|min:0',
            'storage_bags' => 'required|integer|min:0',
            'inbound_boxes' => 'required|integer|min:0',
        ]);

        // Use PricingService for all calculations
        $comparison = $this->pricingService->compareAllOptions(
            $validated['mgt_count'],
            $validated['sgt_count'],
            $validated['kgt_count'],
            $validated['storage_boxes'],
            $validated['storage_bags'],
            $validated['inbound_boxes']
        );
        
        return view('calculator', [
            'result' => [
                'usage' => [
                    'mgt_count' => $validated['mgt_count'],
                    'sgt_count' => $validated['sgt_count'],
                    'kgt_count' => $validated['kgt_count'],
                    'total_shipments' => $validated['mgt_count'] + $validated['sgt_count'] + $validated['kgt_count'],
                    'storage_boxes' => $validated['storage_boxes'],
                    'storage_bags' => $validated['storage_bags'],
                    'inbound_boxes' => $validated['inbound_boxes'],
                ],
                'comparison' => $comparison,
            ]
        ]);
    }
}
