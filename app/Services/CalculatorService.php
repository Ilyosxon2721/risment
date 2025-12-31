<?php

namespace App\Services;

use App\Models\SizeCategory;

class CalculatorService
{
    /**
     * Determine size category based on L+W+H
     */
    public function determineSizeCategory($length, $width, $height)
    {
        $sum = $length + $width + $height;
        
        $category = SizeCategory::where('is_active', true)
            ->where('sum_min', '<=', $sum)
            ->where(function($q) use ($sum) {
                $q->whereNull('sum_max')
                  ->orWhere('sum_max', '>=', $sum);
            })
            ->first();
            
        return $category;
    }
    
    /**
     * Calculate full estimate
     */
    public function calculateTotal($params)
    {
        $breakdown = [];
        $total = 0;
        
        // Size category and logistics
        $category = $this->determineSizeCategory(
            $params['length'],
            $params['width'],
            $params['height']
        );
        
        if ($category) {
            $breakdown['size_category'] = [
                'name' => strtoupper($category->code),
                'price' => $category->price,
                'description' => __('Logistics') . ' (' . strtoupper($category->code) . ')',
            ];
            $total += $category->price;
        }
        
        // Pick & Pack (FBS/DBS only)
       if (in_array($params['scheme'], ['fbs', 'dbs'])) {
            $itemsCount = $params['items_count'] ?? 1;
            $extraItems = $params['extra_items'] ?? 0;
            
            $pickpackCost = 7000 + ($extraItems * 1200);
            
            $breakdown['pickpack'] = [
                'name' => 'Pick & Pack',
                'price' => $pickpackCost,
                'description' => __('Pick and Pack') . ': 7 000 + ' . $extraItems . ' × 1 200',
            ];
            $total += $pickpackCost;
        }
        
        // FBO Shipping
        if ($params['scheme'] === 'fbo') {
            $boxesCount = $params['boxes_count'] ?? 1;
            $fboShipping = 50000 * $boxesCount;
            
            $breakdown['fbo_shipping'] = [
                'name' => __('FBO Shipping'),
                'price' => $fboShipping,
                'description' => __('FBO Shipping') . ': ' . $boxesCount . ' × 50 000',
            ];
            $total += $fboShipping;
        }
        
        // Packaging materials (if selected)
        if (!empty($params['packaging'])) {
            $packagingCosts = [
                'stretch' => 500,
                'ziplock' => 500,
                'safe_bag' => 1200,
                'shrink' => 1000,
                'fragile' => 3000,
                'box_s' => 2500,
                'box_m' => 4000,
                'box_l' => 6000,
            ];
            
            $packagingTotal = 0;
            foreach ($params['packaging'] as $item) {
                if (isset($packagingCosts[$item])) {
                    $packagingTotal += $packagingCosts[$item];
                }
            }
            
            if ($packagingTotal > 0) {
                $breakdown['packaging'] = [
                    'name' => __('Packaging'),
                    'price' => $packagingTotal,
                    'description' => __('Packaging materials'),
                ];
                $total += $packagingTotal;
            }
        }
        
        return [
            'breakdown' => $breakdown,
            'total' => $total,
            'size_category' => $category ? strtoupper($category->code) : 'N/A',
            'dimensions_sum' => $params['length'] + $params['width'] + $params['height'],
        ];
    }
}
