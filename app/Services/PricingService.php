<?php

namespace App\Services;

use App\Models\PricingRate;
use App\Models\SubscriptionPlan;
use App\Models\SurchargeTier;
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
     * Get active surcharge tiers (cached)
     */
    protected function getSurchargeTiers()
    {
        return Cache::remember('pricing.surcharge_tiers', self::CACHE_TTL, function () {
            return SurchargeTier::where('is_active', true)->orderBy('sort')->get();
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
        Cache::forget('pricing.surcharge_tiers');
    }

    /**
     * Calculate per-unit pricing with surcharge
     */
    public function calculatePerUnit(
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxes,
        int $storageBags,
        int $inboundBoxes
    ): array {
        $totalShipments = $mgtCount + $sgtCount + $kgtCount;
        
        // Get rates from database
        $rates = $this->getRates();
        $pickPackFee = $rates['PICKPACK_UNIT']->value;
        $deliveryMgt = $rates['DELIVERY_MGT']->value;
        $deliverySgt = $rates['DELIVERY_SGT']->value;
        $deliveryKgt = $rates['DELIVERY_KGT']->value;
        $storageBoxRate = $rates['STORAGE_BOX']->value;
        $storageBagRate = $rates['STORAGE_BAG']->value;
        $inboundRate = $rates['INBOUND_BOX']->value;
        
        // Rounding (can be made configurable later)
        $rounding = 1000;
        
        // Determine surcharge tier
        $surcharge = $this->getSurchargeRate($totalShipments);
        
        // Calculate base FBS rates (pick&pack + delivery)
        $baseMgt = $pickPackFee + $deliveryMgt;
        $baseSgt = $pickPackFee + $deliverySgt;
        $baseKgt = $pickPackFee + $deliveryKgt;
        
        // Apply surcharge to FBS only and round
        $mgtRate = (int) round(($baseMgt * (1 + $surcharge)) / $rounding) * $rounding;
        $sgtRate = (int) round(($baseSgt * (1 + $surcharge)) / $rounding) * $rounding;
        $kgtRate = (int) round(($baseKgt * (1 + $surcharge)) / $rounding) * $rounding;
        
        // Calculate costs
        $mgtCost = $mgtCount * $mgtRate;
        $sgtCost = $sgtCount * $sgtRate;
        $kgtCost = $kgtCount * $kgtRate;
        $fbsTotal = $mgtCost + $sgtCost + $kgtCost;
        
        // Storage and inbound (NO surcharge)
        $storageCost = ($storageBoxes * $storageBoxRate) + ($storageBags * $storageBagRate);
        $inboundCost = $inboundBoxes * $inboundRate;
        
        $total = $fbsTotal + $storageCost + $inboundCost;
        
        return [
            'mgt' => ['count' => $mgtCount, 'rate' => $mgtRate, 'cost' => $mgtCost],
            'sgt' => ['count' => $sgtCount, 'rate' => $sgtRate, 'cost' => $sgtCost],
            'kgt' => ['count' => $kgtCount, 'rate' => $kgtRate, 'cost' => $kgtCost],
            'fbs_total' => $fbsTotal,
            'storage' => $storageCost,
            'inbound' => $inboundCost,
            'total' => $total,
            'surcharge_percent' => $surcharge * 100,
            'surcharge_tier' => $this->getSurchargeTierName($totalShipments),
        ];
    }
    
    /**
     * Calculate plan cost (monthly fee + overages)
     */
    public function calculatePlanCost(
        SubscriptionPlan $plan,
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxes,
        int $storageBags,
        int $inboundBoxes
    ): array {
        $overage = $plan->calculateOverage(
            $mgtCount, $sgtCount, $kgtCount,
            $storageBoxes, $storageBags, $inboundBoxes
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
     * Calculate recommended monthly fee for a plan
     */
    public function calculateRecommendedMonthlyFee(SubscriptionPlan $plan): int
    {
        if ($plan->is_custom) {
            return 0;
        }
        
        // Target utilization and discount (could be made DB-configurable)
        $targets = [
            'lite' => ['target_util' => 0.80, 'discount' => 0.13],
            'start' => ['target_util' => 0.85, 'discount' => 0.15],
            'pro' => ['target_util' => 0.90, 'discount' => 0.18],
            'business' => ['target_util' => 0.92, 'discount' => 0.20],
        ];
        
        $config = $targets[$plan->code] ?? null;
        if (!$config) {
            return (int) $plan->price_month;
        }
        
        $targetUtil = $config['target_util'];
        $discount = $config['discount'];
        
        // Default shipment mix (could be DB-configurable)
        $mix = ['mgt_ratio' => 0.30, 'sgt_ratio' => 0.50, 'kgt_ratio' => 0.20];
        
        // Get plan limits
        $limits = $plan->limits;
        if (!$limits) {
            return (int) $plan->price_month;
        }
        
        // Calculate target shipment counts
        $targetShipments = (int) round($limits->included_shipments * $targetUtil);
        $mgt = (int) round($targetShipments * $mix['mgt_ratio']);
        $sgt = (int) round($targetShipments * $mix['sgt_ratio']);
        $kgt = (int) round($targetShipments * $mix['kgt_ratio']);
        
        // Calculate per-unit equivalent WITH surcharge
        $perUnit = $this->calculatePerUnit(
            $mgt, $sgt, $kgt,
            $limits->included_boxes ?? 0,
            $limits->included_bags ?? 0,
            $limits->included_inbound_boxes ?? 0
        );
        
        // Apply plan discount
        $targetFee = $perUnit['total'] * (1 - $discount);
        
        // Round to nearest 10k
        $recommendedFee = (int) round($targetFee / 10000) * 10000;
        
        return $recommendedFee;
    }
    
    /**
     * Compare all options and return sorted results with recommendation
     */
    public function compareAllOptions(
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxes,
        int $storageBags,
        int $inboundBoxes
    ): array {
        $comparisons = [];
        
        // Per-unit option
        $perUnit = $this->calculatePerUnit(
            $mgtCount, $sgtCount, $kgtCount,
            $storageBoxes, $storageBags, $inboundBoxes
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
                $storageBoxes, $storageBags, $inboundBoxes
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
     * Get surcharge rate for given shipment volume (from DB)
     */
    private function getSurchargeRate(int $totalShipments): float
    {
        $tiers = $this->getSurchargeTiers();
        
        foreach ($tiers as $tier) {
            $inRange = $totalShipments >= $tier->min_shipments &&
                       ($tier->max_shipments === null || $totalShipments <= $tier->max_shipments);
            
            if ($inRange) {
                return $tier->surcharge_percent / 100; // Convert percentage to decimal
            }
        }
        
        return 0.20; // Default fallback
    }
    
    /**
     * Get human-readable surcharge tier name
     */
    private function getSurchargeTierName(int $totalShipments): string
    {
        if ($totalShipments <= 50) {
            return 'no_surcharge';
        } elseif ($totalShipments <= 300) {
            return 'standard';
        } else {
            return 'high_volume';
        }
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
     * Get overage rates for plan overages (exceeding included limits).
     * Uses base operational rates - NO surcharge tiers applied.
     * 
     * @return array Structured overage data with shipment, storage, and inbound rates
     */
    public function getOverageRates(): array
    {
        $pickpackUnit = $this->getRate('PICKPACK_UNIT');
        $deliveryMGT = $this->getRate('DELIVERY_MGT');
        $deliverySGT = $this->getRate('DELIVERY_SGT');
        $deliveryKGT = $this->getRate('DELIVERY_KGT');
        
        $storageBox = $this->getRate('STORAGE_BOX');
        $storageBag = $this->getRate('STORAGE_BAG');
        $inboundBox = $this->getRate('INBOUND_BOX');

        return [
            'shipments' => [
                'mgt_fee' => $pickpackUnit + $deliveryMGT,  // Base pick&pack + MGT delivery
                'sgt_fee' => $pickpackUnit + $deliverySGT,  // Base pick&pack + SGT delivery
                'kgt_fee' => $pickpackUnit + $deliveryKGT,  // Base pick&pack + KGT delivery
            ],
            'storage' => [
                'box_rate' => $storageBox,  // Per box per month
                'bag_rate' => $storageBag,  // Per bag per month
            ],
            'inbound' => [
                'box_rate' => $inboundBox,  // Per inbound box
            ],
        ];
    }
}
