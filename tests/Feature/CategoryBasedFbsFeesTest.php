<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryBasedFbsFeesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PricingRateSeeder::class);
        $this->seed(\Database\Seeders\RismentRatesSeeder::class);
        PricingService::clearCache();

        // Seed a test plan with base-rate overage fees
        SubscriptionPlan::create([
            'code' => 'test_lite',
            'name_ru' => 'TEST LITE',
            'name_uz' => 'TEST LITE',
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
    public function category_based_overage_applies_included_shipments_by_priority()
    {
        $plan = SubscriptionPlan::where('code', 'test_lite')->first();

        // Test case: 50 MGT, 80 SGT, 20 KGT (total 150, included 120)
        // Allocation: MGT 50 included, SGT 70 included + 10 overage, KGT 20 overage
        $result = $plan->calculateOverage(50, 80, 20, 0, 0, 0);

        $this->assertEquals(690000, $result['total']); // 10×15k + 20×27k
        $this->assertArrayHasKey('shipments', $result['breakdown']);

        $shipments = $result['breakdown']['shipments'];
        $this->assertEquals(0, $shipments['mgt']['count']); // No MGT overage
        $this->assertEquals(0, $shipments['mgt']['fee']);

        $this->assertEquals(10, $shipments['sgt']['count']); // 10 SGT overage
        $this->assertEquals(150000, $shipments['sgt']['fee']); // 10 × 15,000

        $this->assertEquals(20, $shipments['kgt']['count']); // 20 KGT overage
        $this->assertEquals(540000, $shipments['kgt']['fee']); // 20 × 27,000
    }

    /** @test */
    public function per_unit_cost_calculated_by_category()
    {
        $service = app(PricingService::class);

        // 50 MGT, 80 SGT, 20 KGT (150 total, default 10% surcharge)
        $result = $service->calculatePerUnit(50, 80, 20, 0, 0, 0);

        // Each category should have positive costs
        $this->assertGreaterThan(0, $result['mgt']['total']);
        $this->assertGreaterThan(0, $result['sgt']['total']);
        $this->assertGreaterThan(0, $result['kgt']['total']);
        $this->assertGreaterThan(0, $result['fbs_total']);

        // Surcharge is 10% (≤300 shipments)
        $this->assertEquals(10, $result['surcharge_percent']);
        $this->assertEquals('default', $result['surcharge_tier']);

        // FBS total should be sum of category totals
        $this->assertEquals(
            $result['mgt']['total'] + $result['sgt']['total'] + $result['kgt']['total'],
            $result['fbs_total']
        );
    }

    /** @test */
    public function plan_cheaper_than_per_unit_when_within_limits()
    {
        $service = app(PricingService::class);
        $plan = SubscriptionPlan::where('code', 'test_lite')->first();

        // 100 shipments (within 120 included) — no overage expected
        $planCost = $service->calculatePlanCost($plan, 30, 50, 20, 5, 3, 10);

        // No overage since within limits (100 < 120 shipments, storage/inbound at limits)
        $this->assertEquals(0, $planCost['overage']['total']);

        // Total should equal just the monthly fee
        $this->assertEquals((float) $plan->price_month, $planCost['total']);
    }

    /** @test */
    public function overage_with_only_kgt_shipments()
    {
        $plan = SubscriptionPlan::where('code', 'test_lite')->first();

        // Edge case: Only KGT shipments
        $result = $plan->calculateOverage(0, 0, 150, 0, 0, 0);

        // 120 included, 30 overage
        $this->assertEquals(30, $result['breakdown']['shipments']['total_over']);
        $this->assertEquals(30, $result['breakdown']['shipments']['kgt']['count']);
        $this->assertEquals(810000, $result['breakdown']['shipments']['kgt']['fee']); // 30 × 27,000
        $this->assertEquals(810000, $result['total']);
    }

    /** @test */
    public function zero_overage_when_all_within_limits()
    {
        $plan = SubscriptionPlan::where('code', 'test_lite')->first();

        $result = $plan->calculateOverage(30, 40, 30, 0, 0, 0); // Total 100, included 120

        $this->assertEquals(0, $result['total']);
        $this->assertArrayNotHasKey('shipments', $result['breakdown']);
    }

    /** @test */
    public function package_overage_equals_per_unit_pricing()
    {
        // Verify overage uses base rates matching pick&pack + delivery constants
        $mgtPerUnit = SubscriptionPlan::PICK_PACK_FEE + SubscriptionPlan::DELIVERY_RATES['mgt'];
        $sgtPerUnit = SubscriptionPlan::PICK_PACK_FEE + SubscriptionPlan::DELIVERY_RATES['sgt'];
        $kgtPerUnit = SubscriptionPlan::PICK_PACK_FEE + SubscriptionPlan::DELIVERY_RATES['kgt'];

        // Formulas should match
        $this->assertEquals(11000, $mgtPerUnit); // 7k + 4k
        $this->assertEquals(15000, $sgtPerUnit); // 7k + 8k
        $this->assertEquals(27000, $kgtPerUnit); // 7k + 20k

        // Verify actual overage calculation uses matching fees
        $plan = SubscriptionPlan::where('code', 'test_lite')->first();
        $result = $plan->calculateOverage(50, 80, 20, 0, 0, 0); // 30 overage (10 SGT, 20 KGT)

        $expected = (10 * $sgtPerUnit) + (20 * $kgtPerUnit); // 150k + 540k = 690k
        $this->assertEquals($expected, $result['total']);
    }

    /** @test */
    public function model_has_category_fees_in_fillable()
    {
        $plan = new SubscriptionPlan();

        $this->assertContains('over_fbs_mgt_fee', $plan->getFillable());
        $this->assertContains('over_fbs_sgt_fee', $plan->getFillable());
        $this->assertContains('over_fbs_kgt_fee', $plan->getFillable());
    }
}
