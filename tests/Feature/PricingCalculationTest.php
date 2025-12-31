<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingCalculationTest extends TestCase
{
    use RefreshDatabase;
    
    protected PricingService $pricingService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
        $this->pricingService = new PricingService();
    }
    
    /** @test */
    public function small_volume_per_unit_has_no_surcharge()
    {
        // 40 shipments (below 50 threshold)
        $result = $this->pricingService->calculatePerUnit(
            12, 20, 8, // 40 total shipments (mgt, sgt, kgt)
            2, 2, 2    // storage and inbound
        );
        
        $this->assertEquals(0, $result['surcharge_percent']);
        $this->assertEquals('no_surcharge', $result['surcharge_tier']);
    }
    
    /** @test */
    public function medium_volume_per_unit_has_standard_surcharge()
    {
        // 120 shipments (between 50-300)
        $result = $this->pricingService->calculatePerUnit(
            36, 60, 24, // 120 total shipments
            5, 5, 5
        );
        
        $this->assertEquals(10, $result['surcharge_percent']);
        $this->assertEquals('standard', $result['surcharge_tier']);
    }
    
    /** @test */
    public function large_volume_per_unit_has_high_surcharge()
    {
        // 400 shipments (above 300)
        $result = $this->pricingService->calculatePerUnit(
            120, 200, 80, // 400 total shipments
            20, 20, 20
        );
        
        $this->assertEquals(20, $result['surcharge_percent']);
        $this->assertEquals('high_volume', $result['surcharge_tier']);
    }
    
    /** @test */
    public function lite_plan_is_cheaper_than_per_unit_at_80_percent_utilization()
    {
        $litePlan = SubscriptionPlan::where('code', 'lite')->first();
        
        // 80 shipments (80% of 100 included)
        $mgt = 24;
        $sgt = 40;
        $kgt = 16;
        $boxes = 4;
        $bags = 4;
        $inbound = 4;
        
        $perUnit = $this->pricingService->calculatePerUnit($mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        $plan = $this->pricingService->calculatePlanCost($litePlan, $mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        
        // Plan should be 10-15% cheaper
        $this->assertLessThan($perUnit['total'], $plan['total']);
        $savings = ($perUnit['total'] - $plan['total']) / $perUnit['total'];
        $this->assertGreaterThanOrEqual(0.10, $savings, 'LITE plan should be at least 10% cheaper');
        $this->assertLessThanOrEqual(0.20, $savings, 'LITE plan discount should not exceed 20%');
    }
    
    /** @test */
    public function start_plan_is_cheaper_than_per_unit_at_85_percent_utilization()
    {
        $startPlan = SubscriptionPlan::where('code', 'start')->first();
        
        // 255 shipments (85% of 300 included)
        $mgt = 76;
        $sgt = 128;
        $kgt = 51;
        $boxes = 13;
        $bags = 13;
        $inbound = 13;
        
        $perUnit = $this->pricingService->calculatePerUnit($mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        $plan = $this->pricingService->calculatePlanCost($startPlan, $mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        
        // Plan should be 12-18% cheaper
        $this->assertLessThan($perUnit['total'], $plan['total']);
        $savings = ($perUnit['total'] - $plan['total']) / $perUnit['total'];
        $this->assertGreaterThanOrEqual(0.12, $savings, 'START plan should be at least 12% cheaper');
    }
    
    /** @test */
    public function pro_plan_is_cheaper_than_per_unit_at_high_volume()
    {
        $proPlan = SubscriptionPlan::where('code', 'pro')->first();
        
        // 540 shipments (90% of 600 included)
        $mgt = 162;
        $sgt = 270;
        $kgt = 108;
        $boxes = 27;
        $bags = 27;
        $inbound = 27;
        
        $perUnit = $this->pricingService->calculatePerUnit($mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        $plan = $this->pricingService->calculatePlanCost($proPlan, $mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        
        // Plan should be 15-20% cheaper (note: per-unit has 20% surcharge at this volume)
        $this->assertLessThan($perUnit['total'], $plan['total']);
        $savings = ($perUnit['total'] - $plan['total']) / $perUnit['total'];
        $this->assertGreaterThanOrEqual(0.15, $savings, 'PRO plan should be at least 15% cheaper');
    }
    
    /** @test */
    public function comparison_recommends_cheapest_option()
    {
        // Test with LITE-range volume
        $comparison = $this->pricingService->compareAllOptions(
            24, 40, 16, // 80 shipments
            4, 4, 4
        );
        
        $cheapest = $comparison['recommended'];
        $allOptions = collect($comparison['all_options'])->sortBy('total');
        
        $this->assertEquals($cheapest['total'], $allOptions->first()['total']);
    }
    
    /** @test */
    public function plan_overages_use_base_rates_without_surcharge()
    {
        $litePlan = SubscriptionPlan::where('code', 'lite')->first();
        
        // 150 shipments (50 over limit)
        $overage = $litePlan->calculateOverage(
            45, 75, 30, // 150 total
            5, 5, 5
        );
        
        // Verify overage calculation uses base pick&pack (7000) + delivery rates
        // Not the surcharged rates
        $this->assertGreaterThan(0, $overage['total']);
        
        if (isset($overage['breakdown']['shipments'])) {
            // Check that overage fees are reasonable (not surcharged)
            $sgtOverageFee = $overage['breakdown']['shipments']['sgt']['fee'] ?? 0;
            $sgtOverCount = $overage['breakdown']['shipments']['sgt']['count'] ?? 0;
            
            if ($sgtOverCount > 0) {
                $perShipmentRate = $sgtOverageFee / $sgtOverCount;
                // Should be 7000 (pick&pack) + 8000 (delivery) = 15000, not surcharged
                $this->assertEquals(15000, $perShipmentRate);
            }
        }
    }
}
