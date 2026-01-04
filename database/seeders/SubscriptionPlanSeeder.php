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
                'code' => 'lite',
                'name_ru' => 'LITE',
                'name_uz' => 'Yengil',
                'description_ru' => 'Для начинающих продавцов с небольшим объёмом',
                'description_uz' => 'Kichik hajmli boshlang\'ich sotuvchilar uchun',
                'price_month' => 3000000,
                'fbs_shipments_included' => 200,
                'sku_included' => 50,
                'storage_included_boxes' => 300, // 300 box-days
                'storage_included_bags' => 300,  // 300 bag-days
                'inbound_included_boxes' => 10,
                'sla_cutoff_time' => '12:00',
                'schedule_ru' => 'Cut-off 12:00',
                'schedule_uz' => 'Cut-off 12:00',
                'over_fbs_mgt_fee' => 8000,  // Base pick&pack + delivery MGT (4000+4000)
                'over_fbs_sgt_fee' => 15000, // Base pick&pack + delivery SGT (7000+8000)
                'over_fbs_kgt_fee' => 35000, // Base pick&pack + delivery KGT (15000+20000)
                'over_storage_box_fee' => 350,  // Per day
                'over_storage_bag_fee' => 500,  // Per day
                'over_inbound_box_fee' => 15000,
                'sort' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'start',
                'name_ru' => 'START',
                'name_uz' => 'Boshlang\'ich',
                'description_ru' => 'Для растущего бизнеса со средним объёмом',
                'description_uz' => 'O\'rtacha hajmli o\'sib borayotgan biznes uchun',
                'price_month' => 7500000,
                'fbs_shipments_included' => 600,
                'sku_included' => 150,
                'storage_included_boxes' => 900,
                'storage_included_bags' => 900,
                'inbound_included_boxes' => 30,
                'sla_cutoff_time' => '12:00',
                'schedule_ru' => 'Cut-off 12:00',
                'schedule_uz' => 'Cut-off 12:00',
                'over_fbs_mgt_fee' => 8000,
                'over_fbs_sgt_fee' => 15000,
                'over_fbs_kgt_fee' => 35000,
                'over_storage_box_fee' => 500,
                'over_storage_bag_fee' => 350,
                'over_inbound_box_fee' => 15000,
                'sort' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'pro',
                'name_ru' => 'PRO',
                'name_uz' => 'Pro',
                'description_ru' => 'Для профессионалов с высоким объёмом',
                'description_uz' => 'Yuqori hajmli professionallar uchun',
                'price_month' => 16500000,
                'fbs_shipments_included' => 1500,
                'sku_included' => 500,
                'storage_included_boxes' => 2500,
                'storage_included_bags' => 2500,
                'inbound_included_boxes' => 80,
                'sla_cutoff_time' => '14:00',
                'schedule_ru' => 'Cut-off 14:00',
                'schedule_uz' => 'Cut-off 14:00',
                'priority_processing' => true,
                'sla_high' => true,
                'over_fbs_mgt_fee' => 8000,
                'over_fbs_sgt_fee' => 15000,
                'over_fbs_kgt_fee' => 35000,
                'over_storage_box_fee' => 500,
                'over_storage_bag_fee' => 350,
                'over_inbound_box_fee' => 15000,
                'sort' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'business',
                'name_ru' => 'BUSINESS',
                'name_uz' => 'Biznes',
                'description_ru' => 'Для крупных компаний с максимальным объёмом',
                'description_uz' => 'Maksimal hajmli yirik kompaniyalar uchun',
                'price_month' => 34900000,
                'fbs_shipments_included' => 3500,
                'sku_included' => 2000,
                'storage_included_boxes' => 6000,
                'storage_included_bags' => 6000,
                'inbound_included_boxes' => 200,
                'sla_cutoff_time' => '16:00',
                'schedule_ru' => 'Cut-off 16:00',
                'schedule_uz' => 'Cut-off 16:00',
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'over_fbs_mgt_fee' => 8000,
                'over_fbs_sgt_fee' => 15000,
                'over_fbs_kgt_fee' => 35000,
                'over_storage_box_fee' => 500,
                'over_storage_bag_fee' => 350,
                'over_inbound_box_fee' => 15000,
                'sort' => 4,
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
                'min_price_month' => 50000000,
                'schedule_ru' => 'По договорённости',
                'schedule_uz' => 'Kelishuvga binoan',
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'sort' => 5,
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
