<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerUnitSurchargeTest extends TestCase
{
    use RefreshDatabase;

    protected PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PricingRateSeeder::class);
        $this->seed(\Database\Seeders\RismentRatesSeeder::class);
        PricingService::clearCache();
        $this->pricingService = app(PricingService::class);

        // Seed a test plan with base-rate overage fees
        SubscriptionPlan::create([
            'code' => 'test_surcharge',
            'name_ru' => 'TEST',
            'name_uz' => 'TEST',
            'price_month' => 2900000,
            'is_custom' => false,
            'fbs_shipments_included' => 120,
            'storage_included_boxes' => 5,
            'storage_included_bags' => 3,
            'inbound_included_boxes' => 10,
            'shipping_included' => true,
            'over_fbs_mgt_fee' => 11000,  // pick&pack 7k + delivery 4k
            'over_fbs_sgt_fee' => 15000,  // pick&pack 7k + delivery 8k
            'over_fbs_kgt_fee' => 27000,  // pick&pack 7k + delivery 20k
            'over_storage_box_fee' => 18000,
            'over_storage_bag_fee' => 12000,
            'over_inbound_box_fee' => 15000,
            'sort' => 1,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function per_unit_applies_10_percent_surcharge_by_default()
    {
        // 120 MGT shipments (≤300, default 10% surcharge)
        $result = $this->pricingService->calculatePerUnit(120, 0, 0, 0, 0, 0);

        $this->assertEquals(10.0, $result['surcharge_percent']);
        $this->assertEquals('default', $result['surcharge_tier']);
        $this->assertGreaterThan(0, $result['mgt']['total']);

        // Rate should include surcharge
        $this->assertGreaterThan(0, $result['mgt']['rate_per_shipment']);
    }

    /** @test */
    public function per_unit_applies_20_percent_peak_surcharge_above_300()
    {
        // 350 SGT shipments (>300, peak 20% surcharge)
        $result = $this->pricingService->calculatePerUnit(0, 350, 0, 0, 0, 0);

        $this->assertEquals(20.0, $result['surcharge_percent']);
        $this->assertEquals('peak', $result['surcharge_tier']);
        $this->assertGreaterThan(0, $result['sgt']['total']);
    }

    /** @test */
    public function surcharge_not_applied_to_storage_or_inbound()
    {
        // Storage and inbound rates should NOT have surcharge
        $result = $this->pricingService->calculatePerUnit(50, 0, 0, 10, 5, 20);

        // Storage cost: (10 * STORAGE_BOX_DAY) + (5 * STORAGE_BAG_DAY) = 10*500 + 5*350 = 6750
        $storageCost = $result['storage']['cost'];
        $this->assertEquals(10 * 500 + 5 * 350, $storageCost);

        // Inbound cost: 20 * INBOUND_BOX = 20 * 15000 = 300,000
        $inboundCost = $result['inbound']['cost'];
        $this->assertEquals(20 * 15000, $inboundCost);
    }

    /** @test */
    public function package_overage_uses_base_rates_without_surcharge()
    {
        $plan = SubscriptionPlan::where('code', 'test_surcharge')->first();

        // 150 total, 120 included, 30 overage (10 SGT, 20 KGT)
        $result = $plan->calculateOverage(50, 80, 20, 0, 0, 0);

        // Overage should use BASE rates, NOT surcharged rates
        // 10 SGT overage × 15,000 = 150,000
        $this->assertEquals(150000, $result['breakdown']['shipments']['sgt']['fee']);

        // 20 KGT overage × 27,000 = 540,000
        $this->assertEquals(540000, $result['breakdown']['shipments']['kgt']['fee']);

        // Total: 690,000 (NOT higher with surcharge)
        $this->assertEquals(690000, $result['total']);
    }

    /** @test */
    public function packages_cheaper_per_unit_than_per_unit_at_low_volume()
    {
        // Per-unit with surcharge vs package at low volume
        $result = $this->pricingService->calculatePerUnit(100, 0, 0, 5, 3, 10);
        $perUnitTotal = $result['total'];

        // Package costs 2.9M base (covers 120 shipments, storage, inbound)
        $plan = SubscriptionPlan::where('code', 'test_surcharge')->first();
        $planCost = $this->pricingService->calculatePlanCost($plan, 100, 0, 0, 5, 3, 10);

        // For 100 shipments (within 120 limit), per-unit should be cheaper
        // since package costs 2.9M subscription fee
        $this->assertGreaterThan($perUnitTotal, $planCost['total']);

        // Per-unit has surcharge applied
        $this->assertEquals(10, $result['surcharge_percent']);
    }

    /** @test */
    public function kgt_rate_calculated_correctly_with_surcharge()
    {
        // 50 KGT shipments with default 10% surcharge
        $result = $this->pricingService->calculatePerUnit(0, 0, 50, 0, 0, 0);

        // Base KGT: PICKPACK_KGT_FIRST=15000 + DELIVERY_KGT=20000 = 35,000
        // With 10% surcharge: 50 * 35000 * 1.1 ≈ 1,925,000 (ceil rounds to nearest 1000)
        $this->assertEqualsWithDelta(1925000, $result['kgt']['total'], 1000);
        $this->assertEquals(0, $result['kgt']['total'] % 1000, 'Total should be rounded to nearest 1000');
        $this->assertGreaterThan(0, $result['kgt']['rate_per_shipment']);
    }
}
