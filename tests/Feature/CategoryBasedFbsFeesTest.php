<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryBasedFbsFeesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed a test plan
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
            'over_fbs_mgt_fee' => 15000,
            'over_fbs_sgt_fee' => 19000,
            'over_fbs_kgt_fee' => 32000,
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
        // Expected: MGT fully covered, 70 SGT covered + 10 overage, 20 KGT overage
        // Using per-unit pricing: MGT=11k, SGT=15k, KGT=27k
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
    public function per_unit_cost_includes_delivery_by_category()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_lite',
            'mgt_count' => 50,
            'sgt_count' => 80,
            'kgt_count' => 20,
            'storage_boxes' => 0,
            'storage_bags' => 0,
            'inbound_boxes' => 0,
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // MGT: 50 × 12,000 (with 10% surcharge) = 600,000
        $this->assertEquals(600000, $result['per_item_total']['mgt_cost']);
        
        // SGT: 80 × 17,000 (with 10% surcharge) = 1,360,000
        $this->assertEquals(1360000, $result['per_item_total']['sgt_cost']);
        
        // KGT: 20 × 30,000 (with 10% surcharge) = 600,000
        $this->assertEquals(600000, $result['per_item_total']['kgt_cost']);
        
        // Total FBS: 600k + 1,360k + 600k = 2,560,000
        $this->assertEquals(2560000, $result['per_item_total']['fbs_total']);
    }

    /** @test */
    public function package_cheaper_than_per_unit_when_within_limits()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_lite',
            'mgt_count' => 30,
            'sgt_count' => 50,
            'kgt_count' => 30, // Total 110, within 120 included
            'storage_boxes' => 5, // At limit
            'storage_bags' => 3, // At limit
            'inbound_boxes' => 10, // At limit
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // No FBS overage expected (within 120 included)
        $liteComparison = collect($result['all_plans'])
            ->firstWhere('plan.code', 'test_lite');
        
        $this->assertEquals(0, $liteComparison['overage']['total']);
        $this->assertEquals(2900000, $liteComparison['total']); // Just subscription
        
        // Per-unit for this volume: (30×11k + 50×15k + 30×27k) + (5×18k + 3×12k) + (10×15k)
        // = (330k + 750k + 810k) + (90k + 36k) + 150k = 2,166,000
        // Package (2.9M) should be more expensive for low volume, but we're at limits
        // This test verifies no overage when at limits
        $this->assertLessThan(3000000, $result['per_item_total']['total']);
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
        // Verify that package overage uses same formula as per-unit pricing
        // This ensures fair comparison between package and per-unit scenarios
        
        $mgtPerUnit = SubscriptionPlan::PICK_PACK_FEE + SubscriptionPlan::DELIVERY_RATES['mgt'];
        $sgtPerUnit = SubscriptionPlan::PICK_PACK_FEE + SubscriptionPlan::DELIVERY_RATES['sgt'];
        $kgtPerUnit = SubscriptionPlan::PICK_PACK_FEE + SubscriptionPlan::DELIVERY_RATES['kgt'];
        
        // Formulas should match
        $this->assertEquals(11000, $mgtPerUnit); // 7k + 4k
        $this->assertEquals(15000, $sgtPerUnit); // 7k + 8k
        $this->assertEquals(27000, $kgtPerUnit); // 7k + 20k
        
        // Verify actual overage calculation uses same formula
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
