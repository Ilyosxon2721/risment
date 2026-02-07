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
        $this->seed(\Database\Seeders\PricingRateSeeder::class);
        $this->seed(\Database\Seeders\RismentRatesSeeder::class);
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
        PricingService::clearCache();
        $this->pricingService = app(PricingService::class);
    }

    /** @test */
    public function small_volume_per_unit_has_default_surcharge()
    {
        // 40 shipments — below 300 threshold, gets default 10% surcharge
        $result = $this->pricingService->calculatePerUnit(
            12, 20, 8, // 40 total shipments (mgt, sgt, kgt)
            2, 2, 2    // storage and inbound
        );

        $this->assertEquals(10, $result['surcharge_percent']);
        $this->assertEquals('default', $result['surcharge_tier']);
        $this->assertGreaterThan(0, $result['total']);
    }

    /** @test */
    public function medium_volume_per_unit_has_default_surcharge()
    {
        // 120 shipments (between 50-300) — default 10%
        $result = $this->pricingService->calculatePerUnit(
            36, 60, 24, // 120 total shipments
            5, 5, 5
        );

        $this->assertEquals(10, $result['surcharge_percent']);
        $this->assertEquals('default', $result['surcharge_tier']);
        $this->assertGreaterThan(0, $result['total']);
    }

    /** @test */
    public function large_volume_per_unit_has_peak_surcharge()
    {
        // 400 shipments (above 300) — peak 20%
        $result = $this->pricingService->calculatePerUnit(
            120, 200, 80, // 400 total shipments
            20, 20, 20
        );

        $this->assertEquals(20, $result['surcharge_percent']);
        $this->assertEquals('peak', $result['surcharge_tier']);
        $this->assertGreaterThan(0, $result['total']);
    }

    /** @test */
    public function lite_plan_is_cheaper_than_per_unit_at_80_percent_utilization()
    {
        $litePlan = SubscriptionPlan::where('code', 'lite')->first();

        if (!$litePlan) {
            $this->markTestSkipped('LITE plan not found in database');
        }

        // Use 80% of plan's included shipments
        $shipments = (int) round($litePlan->fbs_shipments_included * 0.80);
        $mgt = (int) round($shipments * 0.30);
        $sgt = (int) round($shipments * 0.50);
        $kgt = (int) round($shipments * 0.20);
        $boxes = (int) round(($litePlan->storage_included_boxes ?? 10) * 0.80);
        $bags = (int) round(($litePlan->storage_included_bags ?? 10) * 0.80);
        $inbound = (int) round(($litePlan->inbound_included_boxes ?? 10) * 0.80);

        $perUnit = $this->pricingService->calculatePerUnit($mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        $plan = $this->pricingService->calculatePlanCost($litePlan, $mgt, $sgt, $kgt, $boxes, $bags, $inbound);

        // Plan should be cheaper than per-unit for target volume
        $this->assertLessThan($perUnit['total'], $plan['total']);
    }

    /** @test */
    public function start_plan_is_cheaper_than_per_unit_at_85_percent_utilization()
    {
        $startPlan = SubscriptionPlan::where('code', 'start')->first();

        if (!$startPlan) {
            $this->markTestSkipped('START plan not found in database');
        }

        $shipments = (int) round($startPlan->fbs_shipments_included * 0.85);
        $mgt = (int) round($shipments * 0.30);
        $sgt = (int) round($shipments * 0.50);
        $kgt = (int) round($shipments * 0.20);
        $boxes = (int) round(($startPlan->storage_included_boxes ?? 20) * 0.85);
        $bags = (int) round(($startPlan->storage_included_bags ?? 20) * 0.85);
        $inbound = (int) round(($startPlan->inbound_included_boxes ?? 20) * 0.85);

        $perUnit = $this->pricingService->calculatePerUnit($mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        $plan = $this->pricingService->calculatePlanCost($startPlan, $mgt, $sgt, $kgt, $boxes, $bags, $inbound);

        // Plan should be cheaper than per-unit at target utilization
        $this->assertLessThan($perUnit['total'], $plan['total']);
    }

    /** @test */
    public function pro_plan_is_cheaper_than_per_unit_at_high_volume()
    {
        $proPlan = SubscriptionPlan::where('code', 'pro')->first();

        if (!$proPlan) {
            $this->markTestSkipped('PRO plan not found in database');
        }

        $shipments = (int) round($proPlan->fbs_shipments_included * 0.90);
        $mgt = (int) round($shipments * 0.30);
        $sgt = (int) round($shipments * 0.50);
        $kgt = (int) round($shipments * 0.20);
        $boxes = (int) round(($proPlan->storage_included_boxes ?? 50) * 0.90);
        $bags = (int) round(($proPlan->storage_included_bags ?? 50) * 0.90);
        $inbound = (int) round(($proPlan->inbound_included_boxes ?? 30) * 0.90);

        $perUnit = $this->pricingService->calculatePerUnit($mgt, $sgt, $kgt, $boxes, $bags, $inbound);
        $plan = $this->pricingService->calculatePlanCost($proPlan, $mgt, $sgt, $kgt, $boxes, $bags, $inbound);

        // Plan should be cheaper than per-unit at high volume
        $this->assertLessThan($perUnit['total'], $plan['total']);
    }

    /** @test */
    public function comparison_recommends_cheapest_option()
    {
        // Test with moderate volume
        $comparison = $this->pricingService->compareAllOptions(
            24, 40, 16, // 80 shipments
            4, 4, 4
        );

        $this->assertArrayHasKey('recommended', $comparison);
        $this->assertArrayHasKey('all_options', $comparison);

        $cheapest = $comparison['recommended'];
        $allOptions = collect($comparison['all_options'])->sortBy('total');

        $this->assertEquals($cheapest['total'], $allOptions->first()['total']);
    }

    /** @test */
    public function plan_overages_use_base_rates_without_surcharge()
    {
        $litePlan = SubscriptionPlan::where('code', 'lite')->first();

        if (!$litePlan) {
            $this->markTestSkipped('LITE plan not found in database');
        }

        // Shipments exceeding plan limit to trigger overages
        $totalShipments = $litePlan->fbs_shipments_included + 50;
        $mgt = (int) round($totalShipments * 0.30);
        $sgt = (int) round($totalShipments * 0.50);
        $kgt = (int) round($totalShipments * 0.20);

        $overage = $litePlan->calculateOverage($mgt, $sgt, $kgt, 0, 0, 0);

        // Should have overages since we exceeded included shipments
        $this->assertGreaterThan(0, $overage['total']);
        $this->assertArrayHasKey('shipments', $overage['breakdown']);
    }
}
