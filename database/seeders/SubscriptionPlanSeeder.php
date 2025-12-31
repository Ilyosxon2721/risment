<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Services\PricingService;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $pricingService = new PricingService();
        
        $plans = [
            [
                'code' => 'lite',
                'name_ru' => 'LITE',
                'name_uz' => 'LITE',
                'description_ru' => 'Для начинающих продавцов с небольшим объёмом',
                'description_uz' => 'Kichik hajmdagi sotuvchilar uchun',
                'price_month' => 1400000, // Will be recalculated
                'fbs_shipments_included' => 100,
                'storage_included_boxes' => 5,
                'storage_included_bags' => 5,
                'inbound_included_boxes' => 5,
                'shipping_included' => true,
                'schedule_ru' => '3 раза в неделю: Пн/Ср/Пт, cut-off 12:00',
                'schedule_uz' => 'Haftada 3 marta: Du/Cho/Ju, cut-off 12:00',
                'over_storage_box_fee' => 18000,
                'over_storage_bag_fee' => 12000,
                'over_inbound_box_fee' => 15000,
                'sort' => 1,
            ],
            [
                'code' => 'start',
                'name_ru' => 'START',
                'name_uz' => 'START',
                'description_ru' => 'Для растущего бизнеса со стабильными продажами',
                'description_uz' => 'Barqaror savdo bilan o\'sib borayotgan biznes uchun',
                'price_month' => 4250000, // Will be recalculated
                'fbs_shipments_included' => 300,
                'storage_included_boxes' => 15,
                'storage_included_bags' => 15,
                'inbound_included_boxes' => 15,
                'shipping_included' => true,
                'schedule_ru' => '5 раз в неделю: Пн-Пт, cut-off 15:00',
                'schedule_uz' => 'Haftada 5 marta: Du-Ju, cut-off 15:00',
                'priority_processing' => true,
                'over_storage_box_fee' => 18000,
                'over_storage_bag_fee' => 12000,
                'over_inbound_box_fee' => 15000,
                'sort' => 2,
            ],
            [
                'code' => 'pro',
                'name_ru' => 'PRO',
                'name_uz' => 'PRO',
                'description_ru' => 'Для профессиональных продавцов с высоким оборотом',
                'description_uz' => 'Yuqori aylanma bilan professional sotuvchilar uchun',
                'price_month' => 9840000, // Will be recalculated
                'fbs_shipments_included' => 600,
                'storage_included_boxes' => 30,
                'storage_included_bags' => 30,
                'inbound_included_boxes' => 30,
                'shipping_included' => true,
                'schedule_ru' => 'Ежедневно, cut-off 18:00',
                'schedule_uz' => 'Har kuni, cut-off 18:00',
                'priority_processing' => true,
                'sla_high' => true,
                'over_storage_box_fee' => 18000,
                'over_storage_bag_fee' => 12000,
                'over_inbound_box_fee' => 15000,
                'sort' => 3,
            ],
            [
                'code' => 'business',
                'name_ru' => 'BUSINESS',
                'name_uz' => 'BUSINESS',
                'description_ru' => 'Для крупных продавцов с максимальными объёмами',
                'description_uz' => 'Maksimal hajmli yirik sotuvchilar uchun',
                'price_month' => 29520000, // Will be recalculated
                'fbs_shipments_included' => 2000,
                'storage_included_boxes' => 100,
                'storage_included_bags' => 100,
                'inbound_included_boxes' => 100,
                'shipping_included' => true,
                'schedule_ru' => 'Ежедневно + выходные, cut-off 20:00',
                'schedule_uz' => 'Har kuni + dam olish kunlari, cut-off 20:00',
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'over_storage_box_fee' => 18000,
                'over_storage_bag_fee' => 12000,
                'over_inbound_box_fee' => 15000,
                'sort' => 4,
            ],
        ];
        
        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::updateOrCreate(
                ['code' => $planData['code']],
                $planData
            );
            
            // Calculate and set recommended price
            $recommendedPrice = $pricingService->calculateRecommendedMonthlyFee($plan);
            $plan->update([
                'recommended_price_month' => $recommendedPrice,
                'price_month' => $recommendedPrice, // Use recommended as actual
            ]);
            
            $this->command->info("Plan {$plan->code}: price_month = " . number_format($plan->price_month) . " UZS (recommended: " . number_format($recommendedPrice) . " UZS)");
        }
        
        $this->command->info('Subscription plans seeded successfully!');
    }
}
