<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillingPlansSeeder extends Seeder
{
    /**
     * Seed billing/tariff plans for RISMENT fulfillment.
     *
     * Pricing strategy:
     * - PAYG: No commitment, highest per-operation rates
     * - STARTER: Entry level, small discount
     * - BUSINESS: Most popular, moderate discount
     * - PRO: High volume, significant discount
     * - ENTERPRISE: Custom pricing, maximum discount
     */
    public function run(): void
    {
        $now = now();

        $plans = [
            // ============= PAY AS YOU GO =============
            [
                'code' => 'payg',
                'name_ru' => 'По факту',
                'name_uz' => 'Amaliyot bo\'yicha',
                'description_ru' => 'Платите только за фактически оказанные услуги. Идеально для старта или нерегулярных отправок.',
                'description_uz' => 'Faqat ko\'rsatilgan xizmatlar uchun to\'lang. Boshlash yoki muntazam bo\'lmagan jo\'natmalar uchun ideal.',
                'features_ru' => json_encode([
                    'Без абонентской платы',
                    'Оплата по факту оказания услуг',
                    'Стандартные тарифы',
                    'Доступ к личному кабинету',
                    'Интеграция с маркетплейсами',
                ]),
                'features_uz' => json_encode([
                    'Oylik to\'lovsiz',
                    'Xizmatlar ko\'rsatilgandan keyin to\'lov',
                    'Standart tariflar',
                    'Shaxsiy kabinetga kirish',
                    'Marketpleyslar bilan integratsiya',
                ]),
                'monthly_fee' => 0,
                'storage_rate' => 500,      // 500 сум/коробко-день
                'shipment_rate' => 6000,    // Средняя ставка за отправку
                'receiving_rate' => 15000,  // За приёмку
                'return_rate' => 5000,      // За возврат
                'returns_included' => false,
                'included_storage_units' => 0,
                'included_shipments' => 0,
                'included_receiving_units' => 0,
                'discount_percent' => 0,
                'min_orders_month' => 0,
                'max_orders_month' => null,
                'max_storage_units' => null,
                'free_storage_days' => 0,
                'free_return_days' => 10,
                'billing_model' => 'payg',
                'badge' => null,
                'is_popular' => false,
                'is_visible' => true,
                'is_active' => true,
                'sort' => 1,
            ],

            // ============= STARTER =============
            [
                'code' => 'starter',
                'name_ru' => 'Старт',
                'name_uz' => 'Start',
                'description_ru' => 'Для начинающих продавцов с небольшим объёмом заказов. Выгоднее, чем PAYG при регулярных отправках.',
                'description_uz' => 'Kichik hajmdagi buyurtmalar bilan boshlayotgan sotuvchilar uchun. Muntazam jo\'natmalarda PAYGdan foydaliroq.',
                'features_ru' => json_encode([
                    'До 300 заказов в месяц',
                    'Скидка 5% на все услуги',
                    '50 коробко-дней хранения включено',
                    'Бесплатное хранение 3 дня',
                    'Поддержка по email',
                    'Интеграция с маркетплейсами',
                ]),
                'features_uz' => json_encode([
                    'Oyiga 300 tagacha buyurtma',
                    'Barcha xizmatlarga 5% chegirma',
                    '50 quti-kun saqlash kiritilgan',
                    '3 kun bepul saqlash',
                    'Email orqali yordam',
                    'Marketpleyslar bilan integratsiya',
                ]),
                'monthly_fee' => 990000,    // 990 000 сум/месяц
                'storage_rate' => 475,      // 500 - 5%
                'shipment_rate' => 5700,    // 6000 - 5%
                'receiving_rate' => 14250,  // 15000 - 5%
                'return_rate' => 4750,      // 5000 - 5%
                'returns_included' => false,
                'included_storage_units' => 50,
                'included_shipments' => 0,
                'included_receiving_units' => 0,
                'discount_percent' => 5,
                'min_orders_month' => 0,
                'max_orders_month' => 300,
                'max_storage_units' => 500,
                'free_storage_days' => 3,
                'free_return_days' => 10,
                'billing_model' => 'subscription',
                'badge' => null,
                'is_popular' => false,
                'is_visible' => true,
                'is_active' => true,
                'sort' => 2,
            ],

            // ============= BUSINESS (POPULAR) =============
            [
                'code' => 'business',
                'name_ru' => 'Бизнес',
                'name_uz' => 'Biznes',
                'description_ru' => 'Оптимальный выбор для растущего бизнеса. Хорошие скидки и включённые услуги.',
                'description_uz' => 'O\'sib borayotgan biznes uchun optimal tanlov. Yaxshi chegirmalar va kiritilgan xizmatlar.',
                'features_ru' => json_encode([
                    'До 1 000 заказов в месяц',
                    'Скидка 10% на все услуги',
                    '200 коробко-дней хранения включено',
                    'Бесплатное хранение 5 дней',
                    'Возвраты включены в тариф',
                    'Приоритетная поддержка',
                    'Персональный менеджер',
                    'API доступ',
                ]),
                'features_uz' => json_encode([
                    'Oyiga 1 000 tagacha buyurtma',
                    'Barcha xizmatlarga 10% chegirma',
                    '200 quti-kun saqlash kiritilgan',
                    '5 kun bepul saqlash',
                    'Qaytarishlar tarifga kiritilgan',
                    'Ustuvor yordam',
                    'Shaxsiy menejer',
                    'API kirish',
                ]),
                'monthly_fee' => 2900000,   // 2 900 000 сум/месяц
                'storage_rate' => 450,      // 500 - 10%
                'shipment_rate' => 5400,    // 6000 - 10%
                'receiving_rate' => 13500,  // 15000 - 10%
                'return_rate' => 0,         // Включено
                'returns_included' => true,
                'included_storage_units' => 200,
                'included_shipments' => 0,
                'included_receiving_units' => 0,
                'discount_percent' => 10,
                'min_orders_month' => 100,
                'max_orders_month' => 1000,
                'max_storage_units' => 2000,
                'free_storage_days' => 5,
                'free_return_days' => 14,
                'billing_model' => 'subscription',
                'badge' => 'Популярный',
                'is_popular' => true,
                'is_visible' => true,
                'is_active' => true,
                'sort' => 3,
            ],

            // ============= PRO =============
            [
                'code' => 'pro',
                'name_ru' => 'Про',
                'name_uz' => 'Pro',
                'description_ru' => 'Для опытных продавцов с большим объёмом. Максимальные скидки и премиум-сервис.',
                'description_uz' => 'Katta hajmdagi tajribali sotuvchilar uchun. Maksimal chegirmalar va premium xizmat.',
                'features_ru' => json_encode([
                    'До 3 000 заказов в месяц',
                    'Скидка 15% на все услуги',
                    '500 коробко-дней хранения включено',
                    'Бесплатное хранение 7 дней',
                    'Возвраты включены в тариф',
                    'Приоритетная обработка заказов',
                    'Персональный менеджер',
                    'Расширенная аналитика',
                    'Кастомная упаковка',
                    'API + Webhooks',
                ]),
                'features_uz' => json_encode([
                    'Oyiga 3 000 tagacha buyurtma',
                    'Barcha xizmatlarga 15% chegirma',
                    '500 quti-kun saqlash kiritilgan',
                    '7 kun bepul saqlash',
                    'Qaytarishlar tarifga kiritilgan',
                    'Buyurtmalarni ustuvor ishlash',
                    'Shaxsiy menejer',
                    'Kengaytirilgan tahlil',
                    'Maxsus qadoqlash',
                    'API + Webhooks',
                ]),
                'monthly_fee' => 6900000,   // 6 900 000 сум/месяц
                'storage_rate' => 425,      // 500 - 15%
                'shipment_rate' => 5100,    // 6000 - 15%
                'receiving_rate' => 12750,  // 15000 - 15%
                'return_rate' => 0,         // Включено
                'returns_included' => true,
                'included_storage_units' => 500,
                'included_shipments' => 0,
                'included_receiving_units' => 0,
                'discount_percent' => 15,
                'min_orders_month' => 500,
                'max_orders_month' => 3000,
                'max_storage_units' => 5000,
                'free_storage_days' => 7,
                'free_return_days' => 21,
                'billing_model' => 'subscription',
                'badge' => null,
                'is_popular' => false,
                'is_visible' => true,
                'is_active' => true,
                'sort' => 4,
            ],

            // ============= ENTERPRISE =============
            [
                'code' => 'enterprise',
                'name_ru' => 'Корпоративный',
                'name_uz' => 'Korporativ',
                'description_ru' => 'Индивидуальные условия для крупных клиентов. Максимальные скидки и выделенные ресурсы.',
                'description_uz' => 'Yirik mijozlar uchun individual shartlar. Maksimal chegirmalar va ajratilgan resurslar.',
                'features_ru' => json_encode([
                    'Неограниченное количество заказов',
                    'Скидка 20% на все услуги',
                    '1 000 коробко-дней хранения включено',
                    'Бесплатное хранение 14 дней',
                    'Возвраты включены в тариф',
                    'Выделенная команда',
                    'SLA гарантии',
                    'Индивидуальная интеграция',
                    'Брендированная упаковка',
                    'Полная аналитика и отчёты',
                    'Приоритет 24/7',
                ]),
                'features_uz' => json_encode([
                    'Cheksiz buyurtmalar soni',
                    'Barcha xizmatlarga 20% chegirma',
                    '1 000 quti-kun saqlash kiritilgan',
                    '14 kun bepul saqlash',
                    'Qaytarishlar tarifga kiritilgan',
                    'Ajratilgan jamoa',
                    'SLA kafolatlari',
                    'Individual integratsiya',
                    'Brendlangan qadoqlash',
                    'To\'liq tahlil va hisobotlar',
                    '24/7 ustuvorlik',
                ]),
                'monthly_fee' => 14900000,  // 14 900 000 сум/месяц
                'storage_rate' => 400,      // 500 - 20%
                'shipment_rate' => 4800,    // 6000 - 20%
                'receiving_rate' => 12000,  // 15000 - 20%
                'return_rate' => 0,         // Включено
                'returns_included' => true,
                'included_storage_units' => 1000,
                'included_shipments' => 0,
                'included_receiving_units' => 0,
                'discount_percent' => 20,
                'min_orders_month' => 1000,
                'max_orders_month' => null, // Unlimited
                'max_storage_units' => null, // Unlimited
                'free_storage_days' => 14,
                'free_return_days' => 30,
                'billing_model' => 'subscription',
                'badge' => 'VIP',
                'is_popular' => false,
                'is_visible' => true,
                'is_active' => true,
                'sort' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('billing_plans')->updateOrInsert(
                ['code' => $plan['code']],
                array_merge($plan, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        $this->command->info('Billing plans seeded: ' . count($plans) . ' plans');
    }
}
