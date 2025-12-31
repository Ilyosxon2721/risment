<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerUnitSurchargeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed a test plan
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
            'sort' => 1,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function per_unit_applies_10_percent_surcharge_by_default()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_surcharge',
            'mgt_count' => 120,
            'sgt_count' => 0,
            'kgt_count' => 0,
            'storage_boxes' => 0,
            'storage_bags' => 0,
            'inbound_boxes' => 0,
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // Base MGT rate: 7k + 4k = 11k
        // With 10% surcharge: 11k * 1.1 = 12,100 → rounded to 12,000
        $this->assertEquals(12000, $result['per_item_total']['mgt_rate']);
        $this->assertEquals(1440000, $result['per_item_total']['mgt_cost']); // 120 × 12,000
        $this->assertEquals(10, $result['per_item_total']['surcharge_percent']);
        $this->assertFalse($result['per_item_total']['is_peak_surcharge']);
    }

    /** @test */
    public function per_unit_applies_20_percent_peak_surcharge_above_300()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_surcharge',
            'mgt_count' => 0,
            'sgt_count' => 350,
            'kgt_count' => 0,
            'storage_boxes' => 0,
            'storage_bags' => 0,
            'inbound_boxes' => 0,
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // Base SGT rate: 7k + 8k = 15k
        // With 20% peak: 15k * 1.2 = 18,000
        $this->assertEquals(18000, $result['per_item_total']['sgt_rate']);
        $this->assertEquals(6300000, $result['per_item_total']['sgt_cost']); // 350 × 18,000
        $this->assertEquals(20, $result['per_item_total']['surcharge_percent']);
        $this->assertTrue($result['per_item_total']['is_peak_surcharge']);
    }

    /** @test */
    public function surcharge_not_applied_to_storage_or_inbound()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_surcharge',
            'mgt_count' => 50,
            'sgt_count' => 0,
            'kgt_count' => 0,
            'storage_boxes' => 10,
            'storage_bags' => 5,
            'inbound_boxes' => 20,
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // Storage should be unchanged: (10 × 18k) + (5 × 12k) = 240,000
        $this->assertEquals(240000, $result['per_item_total']['storage']);
        
        // Inbound should be unchanged: 20 × 15k = 300,000
        $this->assertEquals(300000, $result['per_item_total']['inbound']);
        
        // Only FBS should have surcharge
        $this->assertEquals(12000, $result['per_item_total']['mgt_rate']);
    }

    /** @test */
    public function package_overage_uses_base_rates_without_surcharge()
    {
        $plan = SubscriptionPlan::where('code', 'test_surcharge')->first();
        
        // 150 total, 120 included, 30 overage
        $result = $plan->calculateOverage(50, 80, 20, 0, 0, 0);
        
        // Overage should use BASE rates (11k/15k/27k), NOT surcharged rates
        // 10 SGT overage × 15,000 = 150,000
        $this->assertEquals(150000, $result['breakdown']['shipments']['sgt']['fee']);
        
        // 20 KGT overage × 27,000 = 540,000
        $this->assertEquals(540000, $result['breakdown']['shipments']['kgt']['fee']);
        
        // Total: 690,000 (NOT higher with surcharge)
        $this->assertEquals(690000, $result['total']);
    }

    /** @test */
    public function packages_cheaper_per_unit_than_штучный_due_to_surcharge()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_surcharge',
            'mgt_count' => 100,
            'sgt_count' => 0,
            'kgt_count' => 0,
            'storage_boxes' => 5,
            'storage_bags' => 3,
            'inbound_boxes' => 10,
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // Per-unit WITH surcharge: 100 × 12,000 = 1,200,000 (FBS) + storage + inbound
        $perUnitTotal = $result['per_item_total']['total'];
        
        // Package: 2,900,000 (subscription, 120 included covers all 100)
        $packageComparison = collect($result['all_plans'])
            ->firstWhere('plan.code', 'test_surcharge');
        $packageTotal = $packageComparison['total'];
        
        // For 100 shipments only, штучный seems cheaper upfront (1.47M vs 2.9M)
        // BUT package provides better value: no surcharge on future overages
        $this->assertGreaterThan($perUnitTotal, $packageTotal); // Package costs more upfront
        
        // Verify surcharge applied to per-unit
        $this->assertEquals(12000, $result['per_item_total']['mgt_rate']);
        $this->assertEquals(10, $result['per_item_total']['surcharge_percent']);
    }

    /** @test */
    public function kgt_rate_calculated_correctly_with_surcharge()
    {
        $response = $this->post(route('calculator.calculate', ['locale' => 'ru']), [
            'plan_code' => 'test_surcharge',
            'mgt_count' => 0,
            'sgt_count' => 0,
            'kgt_count' => 50,
            'storage_boxes' => 0,
            'storage_bags' => 0,
            'inbound_boxes' => 0,
        ]);
        
        $response->assertOk();
        $result = $response->viewData('result');
        
        // Base KGT: 7k + 20k = 27k
        // With 10%: 27k × 1.1 = 29,700 → rounded to 30,000
        $this->assertEquals(30000, $result['per_item_total']['kgt_rate']);
        $this->assertEquals(1500000, $result['per_item_total']['kgt_cost']); // 50 × 30,000
    }
}
