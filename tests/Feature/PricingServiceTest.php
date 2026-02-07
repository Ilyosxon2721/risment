<?php

namespace Tests\Feature;

use App\Models\PricingRate;
use App\Models\SubscriptionPlan;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PricingRateSeeder::class);
        $this->seed(\Database\Seeders\RismentRatesSeeder::class);
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
        PricingService::clearCache();
    }

    public function test_calculates_per_unit_pricing_with_default_surcharge_tier(): void
    {
        $service = app(PricingService::class);

        $result = $service->calculatePerUnit(
            mgtCount: 40,
            sgtCount: 60,
            kgtCount: 20,
            storageBoxDays: 100,
            storageBagDays: 50,
            inboundBoxes: 10,
            avgItemsPerOrder: 1.0
        );

        $this->assertArrayHasKey('mgt', $result);
        $this->assertArrayHasKey('sgt', $result);
        $this->assertArrayHasKey('kgt', $result);
        $this->assertArrayHasKey('fbs_total', $result);
        $this->assertArrayHasKey('storage', $result);
        $this->assertArrayHasKey('inbound', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('surcharge_percent', $result);
        $this->assertArrayHasKey('surcharge_tier', $result);
        $this->assertEquals(10.0, $result['surcharge_percent']);
        $this->assertEquals('default', $result['surcharge_tier']);
        $this->assertGreaterThan(0, $result['total']);
    }

    public function test_calculates_per_unit_pricing_with_peak_surcharge_tier(): void
    {
        $service = app(PricingService::class);

        $result = $service->calculatePerUnit(
            mgtCount: 100,
            sgtCount: 150,
            kgtCount: 100,
            storageBoxDays: 500,
            storageBagDays: 300,
            inboundBoxes: 30,
            avgItemsPerOrder: 1.0
        );

        $this->assertEquals(20.0, $result['surcharge_percent']);
        $this->assertEquals('peak', $result['surcharge_tier']);
    }

    public function test_calculates_plan_cost_without_surcharge_on_overages(): void
    {
        $service = app(PricingService::class);

        $plan = SubscriptionPlan::where('code', 'lite')->first();

        if (!$plan) {
            $this->markTestSkipped('LITE plan not found in database');
        }

        $result = $service->calculatePlanCost(
            plan: $plan,
            mgtCount: 150,
            sgtCount: 100,
            kgtCount: 50,
            storageBoxDays: 400,
            storageBagDays: 400,
            inboundBoxes: 15
        );

        $this->assertArrayHasKey('plan', $result);
        $this->assertArrayHasKey('monthly_fee', $result);
        $this->assertArrayHasKey('overage', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertEquals((float) $plan->price_month, $result['monthly_fee']);
        $this->assertGreaterThanOrEqual(0, $result['overage']['total']);
        $this->assertEquals($result['monthly_fee'] + $result['overage']['total'], $result['total']);
    }

    public function test_ensures_plan_is_cheaper_than_per_unit_for_target_volume(): void
    {
        $service = app(PricingService::class);

        $plan = SubscriptionPlan::where('code', 'start')->first();

        if (!$plan) {
            $this->markTestSkipped('START plan not found in database');
        }

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

        $this->assertLessThan($perUnit['total'], $planCost['total']);
    }

    public function test_ensures_no_hardcoded_25000_in_overage_fees(): void
    {
        $service = app(PricingService::class);
        $overages = $service->getOverageRates();

        $this->assertNotEquals(25000, $overages['shipments']['mgt_fee']);
        $this->assertNotEquals(25000, $overages['shipments']['sgt_fee']);
        $this->assertNotEquals(25000, $overages['shipments']['kgt_fee']);
    }

    public function test_rounds_operational_fees_up_to_nearest_1000(): void
    {
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

        $this->assertEquals(0, $result['mgt']['total'] % 1000);
        $this->assertEquals(0, $result['sgt']['total'] % 1000);
        $this->assertEquals(0, $result['kgt']['total'] % 1000);
    }
}
