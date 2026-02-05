<?php

namespace App\Services;

use App\Models\PricingRate;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Cache;

class PricingService
{
    protected const CACHE_TTL = 600; // 10 minutes

    /**
     * Get all active pricing rates (cached)
     */
    protected function getRates()
    {
        return Cache::remember('pricing.rates', self::CACHE_TTL, function () {
            return PricingRate::where('is_active', true)->get()->keyBy('code');
        });
    }

    /**
     * Get a single rate by code
     */
    private function getRate(string $code): float
    {
        $rate = $this->getRates()->firstWhere('code', $code);
        return $rate ? (float) $rate->value : 0.0;
    }

    /**
     * Clear all pricing caches
     */
    public static function clearCache(): void
    {
        Cache::forget('pricing.rates');
    }

    /**
     * Determine dimension category from L+W+H sum
     *
     * @param int $dimensions Sum of length + width + height in cm
     * @return string 'micro', 'mgt', 'sgt', or 'kgt'
     */
    public function getDimensionCategory(int $dimensions): string
    {
        $categories = config('pricing.dimension_categories');
        
        foreach ($categories as $code => $range) {
            if ($dimensions >= $range['min'] && $dimensions <= $range['max']) {
                return $code;
            }
        }
        
        return 'kgt'; // Default to largest if out of range
    }

    /**
     * Calculate per-unit (razoviy tarif) pricing with surcharge
     *
     * @param int $mgtCount Number of MGT shipments
     * @param int $sgtCount Number of SGT shipments
     * @param int $kgtCount Number of KGT shipments
     * @param int $storageBoxDays Box-days of storage
     * @param int $storageBagDays Bag-days of storage
     * @param int $inboundBoxes Number of inbound boxes
     * @param float $avgItemsPerOrder Average items per order (default 1)
     * @param int $microCount Number of MICRO shipments
     * @return array
     */
    public function calculatePerUnit(
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxDays = 0,
        int $storageBagDays = 0,
        int $inboundBoxes = 0,
        float $avgItemsPerOrder = 1.0,
        int $microCount = 0
    ): array {
        $totalShipments = $microCount + $mgtCount + $sgtCount + $kgtCount;

        // Determine surcharge tier
        $surcharge = $this->getSurchargeRate($totalShipments);

        // Calculate operational fees per category
        $microOperational = $this->calculateCategoryOperational('micro', $microCount, $avgItemsPerOrder, $surcharge);
        $mgtOperational = $this->calculateCategoryOperational('mgt', $mgtCount, $avgItemsPerOrder, $surcharge);
        $sgtOperational = $this->calculateCategoryOperational('sgt', $sgtCount, $avgItemsPerOrder, $surcharge);
        $kgtOperational = $this->calculateCategoryOperational('kgt', $kgtCount, $avgItemsPerOrder, $surcharge);

        $fbsTotal = $microOperational['total'] + $mgtOperational['total'] + $sgtOperational['total'] + $kgtOperational['total'];

        // Storage and inbound (NO surcharge)
        $storageBoxRate = $this->getRate('STORAGE_BOX_DAY');
        $storageBagRate = $this->getRate('STORAGE_BAG_DAY');
        $storageCost = ($storageBoxDays * $storageBoxRate) + ($storageBagDays * $storageBagRate);

        $inboundRate = $this->getRate('INBOUND_BOX');
        $inboundCost = $inboundBoxes * $inboundRate;

        $total = $fbsTotal + $storageCost + $inboundCost;

        return [
            'micro' => $microOperational,
            'mgt' => $mgtOperational,
            'sgt' => $sgtOperational,
            'kgt' => $kgtOperational,
            'fbs_total' => $fbsTotal,
            'storage' => [
                'box_days' => $storageBoxDays,
                'bag_days' => $storageBagDays,
                'cost' => $storageCost,
            ],
            'inbound' => [
                'boxes' => $inboundBoxes,
                'cost' => $inboundCost,
            ],
            'total' => $total,
            'surcharge_percent' => $surcharge * 100,
            'surcharge_tier' => $this->getSurchargeTierName($totalShipments),
        ];
    }

    /**
     * Calculate operational fees for a specific category
     * Includes Pick&Pack + Delivery, with surcharge applied and rounded up
     */
    private function calculateCategoryOperational(
        string $category,
        int $count,
        float $avgItemsPerOrder,
        float $surcharge
    ): array {
        if ($count === 0) {
            return [
                'count' => 0,
                'orders' => 0,
                'first_items' => 0,
                'additional_items' => 0,
                'rate_per_shipment' => 0,
                'total' => 0,
            ];
        }

        $categoryUpper = strtoupper($category);
        
        // Get rates
        $pickPackFirst = $this->getRate("PICKPACK_{$categoryUpper}_FIRST");
        $pickPackAdd = $this->getRate("PICKPACK_{$categoryUpper}_ADD");
        $delivery = $this->getRate("DELIVERY_{$categoryUpper}");
        
        // Calculate first vs additional items
        $orders = max(1, (int) ceil($count / $avgItemsPerOrder));
        $firstItems = min($count, $orders);
        $additionalItems = max(0, $count - $firstItems);
        
        // Base operational cost
        $baseOperational = 
            ($firstItems * ($pickPackFirst + $delivery)) + 
            ($additionalItems * ($pickPackAdd + $delivery));
        
        // Apply surcharge
        $operationalWithSurcharge = $baseOperational * (1 + $surcharge);
        
        // Round up to nearest 1000
        $rounding = config('pricing.operational_fee_rounding', 1000);
        $total = (int) ceil($operationalWithSurcharge / $rounding) * $rounding;
        
        $ratePerShipment = $count > 0 ? (int) round($total / $count) : 0;
        
        return [
            'count' => $count,
            'orders' => $orders,
            'first_items' => $firstItems,
            'additional_items' => $additionalItems,
            'rate_per_shipment' => $ratePerShipment,
            'total' => $total,
        ];
    }

    /**
     * Get surcharge rate based on total shipments
     * Returns 0.10 (10%) for ≤300 shipments, 0.20 (20%) for >300
     */
    private function getSurchargeRate(int $totalShipments): float
    {
        $threshold = config('pricing.per_unit_surcharge_peak_threshold', 300);
        
        if ($totalShipments > $threshold) {
            return config('pricing.per_unit_surcharge_peak', 0.20);
        }
        
        return config('pricing.per_unit_surcharge_default', 0.10);
    }

    /**
     * Get human-readable surcharge tier name
     */
    private function getSurchargeTierName(int $totalShipments): string
    {
        $threshold = config('pricing.per_unit_surcharge_peak_threshold', 300);
        
        if ($totalShipments > $threshold) {
            return 'peak'; // +20%
        }
        
        return 'default'; // +10%
    }

    /**
     * Calculate plan cost (monthly fee + overages)
     * Plan overages use BASE rates (NO surcharge)
     */
    public function calculatePlanCost(
        SubscriptionPlan $plan,
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxDays,
        int $storageBagDays,
        int $inboundBoxes,
        int $microCount = 0
    ): array {
        $overage = $plan->calculateOverage(
            $mgtCount, $sgtCount, $kgtCount,
            $storageBoxDays, $storageBagDays, $inboundBoxes,
            $microCount
        );
        
        $monthlyFee = $plan->price_month;
        $total = $monthlyFee + $overage['total'];
        
        return [
            'plan' => $plan,
            'monthly_fee' => $monthlyFee,
            'overage' => $overage,
            'total' => $total,
        ];
    }

    /**
     * Compare all options and return sorted results with recommendation
     */
    public function compareAllOptions(
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxDays,
        int $storageBagDays,
        int $inboundBoxes,
        float $avgItemsPerOrder = 1.0,
        int $microCount = 0
    ): array {
        $comparisons = [];

        // Per-unit option
        $perUnit = $this->calculatePerUnit(
            $mgtCount, $sgtCount, $kgtCount,
            $storageBoxDays, $storageBagDays, $inboundBoxes,
            $avgItemsPerOrder,
            $microCount
        );
        
        $comparisons[] = [
            'type' => 'per_unit',
            'plan' => null,
            'total' => $perUnit['total'],
            'breakdown' => $perUnit,
        ];
        
        // All active plans
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('is_custom', false)
            ->with('limits')
            ->orderBy('sort')
            ->get();
            
        foreach ($plans as $plan) {
            $planCost = $this->calculatePlanCost(
                $plan, $mgtCount, $sgtCount, $kgtCount,
                $storageBoxDays, $storageBagDays, $inboundBoxes,
                $microCount
            );
            
            $comparisons[] = [
                'type' => 'plan',
                'plan' => $plan,
                'total' => $planCost['total'],
                'breakdown' => $planCost,
            ];
        }
        
        // Sort by total
        $sorted = collect($comparisons)->sortBy('total')->values();
        $perUnitTotal = collect($comparisons)->where('type', 'per_unit')->first()['total'];
        
        // Add savings calculations to ALL options
        $sorted = $sorted->map(function ($option) use ($perUnitTotal) {
            $savingsAmount = $perUnitTotal - $option['total'];
            $savingsPercent = $perUnitTotal > 0 ? ($savingsAmount / $perUnitTotal) * 100 : 0;
            
            return array_merge($option, [
                'savings_vs_per_unit' => $savingsAmount,
                'savings_percent' => $savingsPercent,
            ]);
        });
        
        // Find cheapest AFTER adding savings data
        $cheapest = $sorted->first();
        
        return [
            'all_options' => $sorted->all(),
            'recommended' => $cheapest,
            'per_unit_total' => $perUnitTotal,
        ];
    }

    /**
     * Get overage rates for plan overages (exceeding included limits).
     * Uses base operational rates - NO surcharge tiers applied.
     *
     * @return array Structured overage data with shipment, storage, and inbound rates
     */
    public function getOverageRates(): array
    {
        // Base Pick&Pack + Delivery rates (no surcharge)
        $microPickPack = $this->getRate('PICKPACK_MICRO_FIRST');
        $mgtPickPack = $this->getRate('PICKPACK_MGT_FIRST');
        $sgtPickPack = $this->getRate('PICKPACK_SGT_FIRST');
        $kgtPickPack = $this->getRate('PICKPACK_KGT_FIRST');

        $deliveryMICRO = $this->getRate('DELIVERY_MICRO');
        $deliveryMGT = $this->getRate('DELIVERY_MGT');
        $deliverySGT = $this->getRate('DELIVERY_SGT');
        $deliveryKGT = $this->getRate('DELIVERY_KGT');

        $storageBoxDay = $this->getRate('STORAGE_BOX_DAY');
        $storageBagDay = $this->getRate('STORAGE_BAG_DAY');
        $inboundBox = $this->getRate('INBOUND_BOX');

        return [
            'shipments' => [
                'micro_fee' => $microPickPack + $deliveryMICRO,
                'mgt_fee' => $mgtPickPack + $deliveryMGT,
                'sgt_fee' => $sgtPickPack + $deliverySGT,
                'kgt_fee' => $kgtPickPack + $deliveryKGT,
            ],
            'storage' => [
                'box_rate' => $storageBoxDay,
                'bag_rate' => $storageBagDay,
            ],
            'inbound' => [
                'box_rate' => $inboundBox,
            ],
        ];
    }

    /**
     * Get public rates for display in views
     */
    public function getPublicRates(): array
    {
        $rates = $this->getRates();
        return $rates->toArray();
    }

    /**
     * Calculate recommended monthly fee for a plan
     */
    public function calculateRecommendedMonthlyFee(SubscriptionPlan $plan): int
    {
        if ($plan->is_custom) {
            return 0;
        }
        
        $targets = config('pricing.plan_targets', []);
        $config = $targets[$plan->code] ?? null;
        
        if (!$config) {
            return (int) $plan->price_month;
        }
        
        $targetUtil = $config['target_util'];
        $discount = $config['discount'];

        $mix = config('pricing.default_shipment_mix');

        // Calculate target shipment counts
        $targetShipments = (int) round($plan->fbs_shipments_included * $targetUtil);
        $micro = (int) round($targetShipments * ($mix['micro_ratio'] ?? 0));
        $mgt = (int) round($targetShipments * $mix['mgt_ratio']);
        $sgt = (int) round($targetShipments * $mix['sgt_ratio']);
        $kgt = (int) round($targetShipments * $mix['kgt_ratio']);

        // Calculate per-unit equivalent WITH surcharge
        $perUnit = $this->calculatePerUnit(
            $mgt, $sgt, $kgt,
            $plan->storage_included_boxes ?? 0,
            $plan->storage_included_bags ?? 0,
            $plan->inbound_included_boxes ?? 0,
            1.0,
            $micro
        );
        
        // Apply plan discount
        $targetFee = $perUnit['total'] * (1 - $discount);
        
        // Round to nearest value from config
        $rounding = config('pricing.plan_fee_rounding', 10000);
        $recommendedFee = (int) round($targetFee / $rounding) * $rounding;
        
        return $recommendedFee;
    }
}
