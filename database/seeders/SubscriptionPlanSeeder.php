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
                'billing_model' => 'subscription',
                'name_ru' => 'Starter',
                'name_uz' => 'Starter',
                'description_ru' => 'Для начинающих продавцов с небольшим объёмом',
                'description_uz' => 'Kichik hajmli boshlang\'ich sotuvchilar uchun',
                'price_month' => 2900000,
                'fbs_shipments_included' => 300,
                'storage_included_boxes' => 500,
                'inbound_included_boxes' => 1000,
                'shipping_included' => true,
                'over_fbs_mgt_fee' => 5000,
                'over_fbs_sgt_fee' => 5000,
                'over_fbs_kgt_fee' => 5000,
                'over_storage_box_fee' => 100,
                'over_inbound_box_fee' => 200,
                'sort' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'business',
                'billing_model' => 'subscription',
                'name_ru' => 'Business',
                'name_uz' => 'Business',
                'description_ru' => 'Для растущего бизнеса со средним объёмом',
                'description_uz' => 'O\'rtacha hajmli o\'sib borayotgan biznes uchun',
                'price_month' => 7900000,
                'fbs_shipments_included' => 1000,
                'storage_included_boxes' => 2000,
                'inbound_included_boxes' => 5000,
                'shipping_included' => true,
                'over_fbs_mgt_fee' => 4000,
                'over_fbs_sgt_fee' => 4000,
                'over_fbs_kgt_fee' => 4000,
                'over_storage_box_fee' => 80,
                'over_inbound_box_fee' => 150,
                'sort' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'enterprise',
                'billing_model' => 'subscription',
                'name_ru' => 'Enterprise',
                'name_uz' => 'Enterprise',
                'description_ru' => 'Для крупных компаний с максимальным объёмом',
                'description_uz' => 'Maksimal hajmli yirik kompaniyalar uchun',
                'price_month' => 19900000,
                'fbs_shipments_included' => 5000,
                'storage_included_boxes' => 10000,
                'inbound_included_boxes' => 20000,
                'shipping_included' => true,
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'over_fbs_mgt_fee' => 3000,
                'over_fbs_sgt_fee' => 3000,
                'over_fbs_kgt_fee' => 3000,
                'over_storage_box_fee' => 50,
                'over_inbound_box_fee' => 100,
                'sort' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'payg',
                'billing_model' => 'payg',
                'name_ru' => 'Pay as you go',
                'name_uz' => 'Pay as you go',
                'description_ru' => 'Оплата по факту без абонентской платы',
                'description_uz' => 'Oylik to\'lovsiz faqat foydalanishga qarab to\'lov',
                'price_month' => 0,
                'fbs_shipments_included' => 0,
                'storage_included_boxes' => 0,
                'inbound_included_boxes' => 0,
                'shipping_included' => false,
                'over_fbs_mgt_fee' => 6000,
                'over_fbs_sgt_fee' => 6000,
                'over_fbs_kgt_fee' => 6000,
                'over_storage_box_fee' => 150,
                'over_inbound_box_fee' => 300,
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
