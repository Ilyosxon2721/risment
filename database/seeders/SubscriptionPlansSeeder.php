<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $schedule_ru = "3 раза в неделю: Пн/Ср/Пт, cut-off 12:00";
        $schedule_uz = "Haftada 3 marta: Dush/Chor/Juma, cut-off 12:00";

        $plans = [
            [
                'code' => 'lite',
                'name_ru' => 'LITE',
                'name_uz' => 'LITE',
                'description_ru' => 'Для продавцов с ~100 заказами/мес. Включено: FBS до 120 отправок, хранение 5 коробов/3 мешка, приёмка 10 коробов.',
                'description_uz' => '~100 buyurtma/oy sotuvchilar uchun. FBS 120 tagacha jo\'natish, 5 quti/3 qop saqlash, 10 quti qabul qilish.',
                'price_month' => 2900000,
                'is_custom' => false,
                'fbs_shipments_included' => 120,
                'storage_included_boxes' => 5,
                'storage_included_bags' => 3,
                'inbound_included_boxes' => 10,
                'shipping_included' => true,
                'schedule_ru' => $schedule_ru,
                'schedule_uz' => $schedule_uz,
                'priority_processing' => false,
                'sla_high' => false,
                'personal_manager' => false,
                'over_fbs_mgt_fee' => 15000,
                'over_fbs_sgt_fee' => 19000,
                'over_fbs_kgt_fee' => 32000,
                'sort' => 1,
            ],
            [
                'code' => 'start',
                'name_ru' => 'START',
                'name_uz' => 'START',
                'description_ru' => 'Оптимально для стабильного потока. FBS до 250 отправок, хранение 10 коробов/5 мешков, приёмка 20 коробов.',
                'description_uz' => 'Barqaror oqim uchun optimal. FBS 250 tagacha jo\'natish, 10 quti/5 qop saqlash, 20 quti qabul qilish.',
                'price_month' => 5900000,
                'is_custom' => false,
                'fbs_shipments_included' => 250,
                'storage_included_boxes' => 10,
                'storage_included_bags' => 5,
                'inbound_included_boxes' => 20,
                'shipping_included' => true,
                'schedule_ru' => $schedule_ru,
                'schedule_uz' => $schedule_uz,
                'priority_processing' => false,
                'sla_high' => false,
                'personal_manager' => false,
                'over_fbs_mgt_fee' => 15000,
                'over_fbs_sgt_fee' => 19000,
                'over_fbs_kgt_fee' => 32000,
                'sort' => 2,
            ],
            [
                'code' => 'pro',
                'name_ru' => 'PRO',
                'name_uz' => 'PRO',
                'description_ru' => 'Для роста и регулярных отгрузок. FBS до 700 отправок, приоритет обработки, хранение 50 коробов/25 мешков.',
                'description_uz' => 'O\'sish va muntazam jo\'natishlar uchun. FBS 700 tagacha, ustivor ishlov, 50 quti/25 qop saqlash.',
                'price_month' => 12900000,
                'is_custom' => false,
                'fbs_shipments_included' => 700,
                'storage_included_boxes' => 50,
                'storage_included_bags' => 25,
                'inbound_included_boxes' => 60,
                'shipping_included' => true,
                'schedule_ru' => $schedule_ru,
                'schedule_uz' => $schedule_uz,
                'priority_processing' => true,
                'sla_high' => false,
                'personal_manager' => false,
                'over_fbs_mgt_fee' => 15000,
                'over_fbs_sgt_fee' => 19000,
                'over_fbs_kgt_fee' => 32000,
                'sort' => 3,
            ],
            [
                'code' => 'business',
                'name_ru' => 'BUSINESS',
                'name_uz' => 'BUSINESS',
                'description_ru' => 'Для крупных продавцов. FBS до 1800 отправок, высокий SLA, персональный менеджер, хранение 150 коробов/70 мешков.',
                'description_uz' => 'Yirik sotuvchilar uchun. FBS 1800 tagacha, yuqori SLA, shaxsiy menejer, 150 quti/70 qop saqlash.',
                'price_month' => 29900000,
                'is_custom' => false,
                'fbs_shipments_included' => 1800,
                'storage_included_boxes' => 150,
                'storage_included_bags' => 70,
                'inbound_included_boxes' => 150,
                'shipping_included' => true,
                'schedule_ru' => $schedule_ru,
                'schedule_uz' => $schedule_uz,
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'over_fbs_mgt_fee' => 15000,
                'over_fbs_sgt_fee' => 19000,
                'over_fbs_kgt_fee' => 32000,
                'sort' => 4,
            ],
            [
                'code' => 'enterprise',
                'name_ru' => 'ENTERPRISE',
                'name_uz' => 'ENTERPRISE',
                'description_ru' => 'Индивидуальные условия под большие объёмы. Гибкие лимиты, выделенная команда, интеграции.',
                'description_uz' => 'Katta hajmlar uchun individual shartlar. Moslashuvchan limitlar, maxsus jamoa, integratsiyalar.',
                'price_month' => 55000000,
                'is_custom' => true,
                'min_price_month' => 55000000,
                'fbs_shipments_included' => null,
                'storage_included_boxes' => null,
                'storage_included_bags' => null,
                'inbound_included_boxes' => null,
                'shipping_included' => true,
                'schedule_ru' => 'По согласованию',
                'schedule_uz' => 'Kelishuv bo\'yicha',
                'priority_processing' => true,
                'sla_high' => true,
                'personal_manager' => true,
                'over_fbs_mgt_fee' => 15000,
                'over_fbs_sgt_fee' => 19000,
                'over_fbs_kgt_fee' => 32000,
                'sort' => 5,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['code' => $planData['code']],
                $planData
            );
        }
    }
}
