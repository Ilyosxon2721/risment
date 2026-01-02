<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'code' => 'starter',
                'name_ru' => 'Starter',
                'name_uz' => 'Starter',
                'description_ru' => 'Идеально для начинающих продавцов с небольшим объёмом',
                'description_uz' => 'Kichik hajmli boshlang\'ich sotuvchilar uchun ideal',
                'price_month' => 490000,
                'fbs_shipments_included' => 50,
                'storage_included_boxes' => 20,
                'storage_included_bags' => 10,
                'inbound_included_boxes' => 10,
                'schedule_ru' => '2 раза в неделю (Вт/Пт)',
                'schedule_uz' => 'Haftada 2 marta (Se/Ju)',
                'over_fbs_mgt_fee' => 11000,
                'over_fbs_sgt_fee' => 15000,
                'over_fbs_kgt_fee' => 27000,
                'over_storage_box_fee' => 300,
                'over_storage_bag_fee' => 500,
                'over_inbound_box_fee' => 3000,
                'sort' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'growth',
                'name_ru' => 'Growth',
                'name_uz' => 'Growth',
                'description_ru' => 'Для растущего бизнеса со средним объёмом продаж',
                'description_uz' => 'O\'rtacha savdo hajmiga ega o\'sib borayotgan biznes uchun',
                'price_month' => 990000,
                'recommended_price_month' => 990000,
                'fbs_shipments_included' => 150,
                'storage_included_boxes' => 50,
                'storage_included_bags' => 30,
                'inbound_included_boxes' => 30,
                'schedule_ru' => '3 раза в неделю (Пн/Ср/Пт)',
                'schedule_uz' => 'Haftada 3 marta (Du/Ch/Ju)',
                'over_fbs_mgt_fee' => 10000,
                'over_fbs_sgt_fee' => 14000,
                'over_fbs_kgt_fee' => 25000,
                'over_storage_box_fee' => 250,
                'over_storage_bag_fee' => 400,
                'over_inbound_box_fee' => 2500,
                'sort' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'pro',
                'name_ru' => 'Pro',
                'name_uz' => 'Pro',
                'description_ru' => 'Для профессионалов с высоким объёмом продаж',
                'description_uz' => 'Yuqori savdo hajmiga ega professionallar uchun',
                'price_month' => 1990000,
                'fbs_shipments_included' => 400,
                'storage_included_boxes' => 150,
                'storage_included_bags' => 80,
                'inbound_included_boxes' => 80,
                'schedule_ru' => '6 раз в неделю (Пн-Сб)',
                'schedule_uz' => 'Haftada 6 marta (Du-Sha)',
                'priority_processing' => true,
                'sla_high' => true,
                'over_fbs_mgt_fee' => 9000,
                'over_fbs_sgt_fee' => 13000,
                'over_fbs_kgt_fee' => 23000,
                'over_storage_box_fee' => 200,
                'over_storage_bag_fee' => 350,
                'over_inbound_box_fee' => 2000,
                'sort' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'enterprise',
                'name_ru' => 'Enterprise',
                'name_uz' => 'Enterprise',
                'description_ru' => 'Индивидуальные условия для крупного бизнеса',
                'description_uz' => 'Yirik biznes uchun individual shartlar',
                'price_month' => 0,
                'is_custom' => true,
                'schedule_ru' => 'По договорённости',
                'schedule_uz' => 'Kelishuvga binoan',
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'sort' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['code' => $plan['code']],
                $plan
            );
        }
    }
}
