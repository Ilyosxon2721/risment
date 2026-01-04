<?php

use App\Services\PricingService;
use App\Models\PricingRate;
use App\Models\SubscriptionPlan;

describe('PricingService', function () {
    beforeEach(function () {
        // Clear cache before each test
        PricingService::clearCache();
    });

    it('calculates per-unit pricing with default surcharge tier', function () {
        $service = app(PricingService::class);
        
        // Test with 120 shipments (should get +10% surcharge)
        $result = $service->calculatePerUnit(
            mgtCount: 40,
            sgtCount: 60,
            kgtCount: 20,
            storageBoxDays: 100,
            storageBagDays: 50,
            inboundBoxes: 10,
            avgItemsPerOrder: 1.0
        );
        
        expect($result)->toHaveKeys(['mgt', 'sgt', 'kgt', 'fbs_total', 'storage', 'inbound', 'total', 'surcharge_percent', 'surcharge_tier']);
        expect($result['surcharge_percent'])->toBe(10.0);
        expect($result['surcharge_tier'])->toBe('default');
        expect($result['total'])->toBeGreaterThan(0);
    });

    it('calculates per-unit pricing with peak surcharge tier', function () {
        $service = app(PricingService::class);
        
        // Test with 350 shipments (should get +20% surcharge)
        $result = $service->calculatePerUnit(
            mgtCount: 100,
            sgtCount: 150,
            kgtCount: 100,
            storageBoxDays: 500,
            storageBagDays: 300,
            inboundBoxes: 30,
            avgItemsPerOrder: 1.0
        );
        
        expect($result['surcharge_percent'])->toBe(20.0);
        expect($result['surcharge_tier'])->toBe('peak');
    });

    it('calculates plan cost without surcharge on overages', function () {
        $service = app(PricingService::class);
        
        $plan = SubscriptionPlan::where('code', 'lite')->first();
        
        if (!$plan) {
            $this->markTestSkipped('LITE plan not found in database');
        }
        
        // Test with usage exceeding plan limits
        $result = $service->calculatePlanCost(
            plan: $plan,
            mgtCount: 150,  // Exceeds 200 limit combined
            sgtCount: 100,
            kgtCount: 50,
            storageBoxDays: 400,  // Exceeds 300 limit
            storageBagDays: 400,  // Exceeds 300 limit
            inboundBoxes: 15  // Exceeds 10 limit
        );
        
        expect($result)->toHaveKeys(['plan', 'monthly_fee', 'overage', 'total']);
        expect($result['monthly_fee'])->toBe((float) $plan->price_month);
        expect($result['overage']['total'])->toBeGreaterThan(0);
        expect($result['total'])->toBe($result['monthly_fee'] + $result['overage']['total']);
    });

    it('ensures plan is cheaper than per-unit for target volume', function () {
        $service = app(PricingService::class);
        
        $plan = SubscriptionPlan::where('code', 'start')->first();
        
        if (!$plan) {
            $this->markTestSkipped('START plan not found in database');
        }
        
        // Use 85% of plan limits (typical usage)
        $shipments = (int) round($plan->fbs_shipments_included * 0.85);
        $mgt = (int) round($shipments * 0.30);
        $sgt = (int) round($shipments * 0.50);
        $kgt = (int) round($shipments * 0.20);
        
        $storageBoxDays = (int) round($plan->storage_included_boxes * 0.85);
        $storageBagDays = (int) round($plan->storage_included_bags * 0.85);
        $inboundBoxes = (int) round($plan->inbound_included_boxes * 0.85);
        
        $perUnit = $service->calculatePerUnit(
            $mgt, $sgt, $kgt,
            $storageBoxDays, $storageBagDays, $inboundBoxes
        );
        
        $planCost = $service->calculatePlanCost(
            $plan,
            $mgt, $sgt, $kgt,
            $storageBoxDays, $storageBagDays, $inboundBoxes
        );
        
        // Plan should be cheaper than per-unit at target utilization
        expect($planCost['total'])->toBeLessThan($perUnit['total']);
    });

    it('ensures no hardcoded 25000 in overage fees', function () {
        $service = app(PricingService::class);
        $overages = $service->getOverageRates();
        
        // Check that no overage fee is exactly 25000
        expect($overages['shipments']['mgt_fee'])->not->toBe(25000);
        expect($overages['shipments']['sgt_fee'])->not->toBe(25000);
        expect($overages['shipments']['kgt_fee'])->not->toBe(25000);
    });

    it('rounds operational fees up to nearest 1000', function () {
        $service = app(PricingService::class);
        
        $result = $service->calculatePerUnit(
            mgtCount: 10,
            sgtCount: 10,
            kgtCount: 10,
            storageBoxDays: 0,
            storageBagDays: 0,
            inboundBoxes: 0,
            avgItemsPerOrder: 1.0
        );
        
        // All operational costs should be multiples of 1000
        expect($result['mgt']['total'] % 1000)->toBe(0);
        expect($result['sgt']['total'] % 1000)->toBe(0);
        expect($result['kgt']['total'] % 1000)->toBe(0);
    });
});
