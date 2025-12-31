<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use Illuminate\Database\Seeder;

class CmsPricingCalculatorContentSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            // PRICING PAGE
            [
                'page_slug' => 'pricing',
                'block_key' => 'hero_title',
                'title_ru' => 'Тарифы RISMENT — всё включено в месяц',
                'title_uz' => 'RISMENT tariflari — oyiga hammasi ichida',
                'sort' => 1,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'hero_subtitle',
                'body_ru' => 'Сборка, упаковка, хранение и доставка FBS по графику 3 раза в неделю. Прозрачно: лимиты и переплаты фиксированы.',
                'body_uz' => 'Yig\'ish, qadoqlash, saqlash va FBS yetkazib berish haftasiga 3 marta. Hammasi aniq: limitlar va ortiqcha to\'lovlar belgilangan.',
                'sort' => 2,
            ],
            
            // What's included
            [
                'page_slug' => 'pricing',
                'block_key' => 'included_title',
                'title_ru' => 'Что входит в тариф',
                'title_uz' => 'Tarifga nima kiradi',
                'sort' => 3,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'included_bullets',
                'body_ru' => 'FBS обработка заказов (Pick&Pack) в рамках лимита|Доставка FBS включена (Пн/Ср/Пт, cut-off 12:00)|Хранение в пределах включённого объёма|Приёмка поставок в пределах включённого объёма|Кабинет: остатки, заявки, статусы',
                'body_uz' => 'Limit doirasida FBS buyurtmalarni tayyorlash (Pick&Pack)|FBS yetkazib berish ichida (Du/Chor/Ju, cut-off 12:00)|Limit doirasida saqlash|Limit doirasida qabul (inbound)|Kabinet: qoldiq, arizalar, statuslar',
                'sort' => 4,
            ],
            
            // What's NOT included
            [
                'page_slug' => 'pricing',
                'block_key' => 'not_included_title',
                'title_ru' => 'Что НЕ входит',
                'title_uz' => 'Nima kirmaydi',
                'sort' => 5,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'not_included_bullets',
                'body_ru' => 'DBS доставка (по договорённости отдельно)|FBO поставки на склад маркетплейса|Нестандарт: хрупкое/негабарит/особые требования — индивидуально|Возвраты/брак (reverse) — отдельные условия',
                'body_uz' => 'DBS yetkazib berish (alohida kelishuv)|Marketplace omboriga FBO jo\'natmalar|Nostandart: mo\'rt/katta gabarit/maxsus talablar — individual|Qaytish/brak (reverse) — alohida shartlar',
                'sort' => 6,
            ],
            
            // Plan taglines
            [
                'page_slug' => 'pricing',
                'block_key' => 'plan_lite_tagline',
                'body_ru' => 'Для продавцов с ~100 отправками в месяц.',
                'body_uz' => 'Oyiga taxminan 100 ta jo\'natma qiladigan sotuvchilar uchun.',
                'sort' => 10,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'plan_start_tagline',
                'body_ru' => 'Оптимально для стабильного потока от 200 отправок.',
                'body_uz' => '200+ jo\'natma bo\'lgan barqaror oqim uchun.',
                'sort' => 11,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'plan_pro_tagline',
                'body_ru' => 'Для роста и регулярных отгрузок.',
                'body_uz' => 'O\'sish va muntazam jo\'natmalar uchun.',
                'sort' => 12,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'plan_business_tagline',
                'body_ru' => 'Для крупных продавцов и высокого SLA.',
                'body_uz' => 'Katta sotuvchilar va yuqori SLA uchun.',
                'sort' => 13,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'plan_enterprise_tagline',
                'body_ru' => 'Индивидуально под большие объёмы.',
                'body_uz' => 'Katta hajmlar uchun individual.',
                'sort' => 14,
            ],
            
            // Overages
            [
            // Overages section removed - now dynamically rendered from PricingService in pricing.blade.php
            
            // Schedule
            [
                'page_slug' => 'pricing',
                'block_key' => 'schedule_title',
                'title_ru' => 'График отгрузок',
                'title_uz' => 'Jo\'natish jadvali',
                'sort' => 30,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'schedule_body',
                'body_ru' => 'Отгрузки FBS: 3 раза в неделю (Пн/Ср/Пт). Cut-off в день отгрузки: до 12:00.',
                'body_uz' => 'FBS jo\'natmalar: haftasiga 3 marta (Du/Chor/Ju). Jo\'natish kuni cut-off: 12:00 gacha.',
                'sort' => 31,
            ],
            
            // Policy
            [
                'page_slug' => 'pricing',
                'block_key' => 'policy_upgrade',
                'body_ru' => 'Если превышение лимитов повторяется 2 месяца подряд — перевод на следующий тариф или пересмотр условий.',
                'body_uz' => 'Agar limitlar ketma-ket 2 oy oshsa — keyingi tarifga o\'tkazish yoki shartlarni qayta ko\'rib chiqish.',
                'sort' => 40,
            ],
            
            // CTA
            [
                'page_slug' => 'pricing',
                'block_key' => 'cta_quote',
                'title_ru' => 'Получить расчёт',
                'title_uz' => 'Hisob-kitob olish',
                'sort' => 50,
            ],
            [
                'page_slug' => 'pricing',
                'block_key' => 'cta_client',
                'title_ru' => 'Стать клиентом',
                'title_uz' => 'Mijoz bo\'lish',
                'sort' => 51,
            ],
            
            // CALCULATOR PAGE
            [
                'page_slug' => 'calculator',
                'block_key' => 'explanation',
                'body_ru' => 'Выберите тариф и укажите ожидаемые объёмы. Мы покажем итог и возможные переплаты.',
                'body_uz' => 'Tarifni tanlang va kutilayotgan hajmlarni kiriting. Yakuniy summa va ehtimoliy ortiqcha to\'lovlarni ko\'rsatamiz.',
                'sort' => 1,
            ],
            
            // Input labels
            [
                'page_slug' => 'calculator',
                'block_key' => 'label_plan',
                'title_ru' => 'Тариф',
                'title_uz' => 'Tarif',
                'sort' => 10,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'label_shipments',
                'title_ru' => 'Отправок FBS в месяц',
                'title_uz' => 'Oyiga FBS jo\'natmalar',
                'sort' => 11,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'label_storage_boxes',
                'title_ru' => 'Коробов 60×40×40',
                'title_uz' => '60×40×40 koroblar',
                'sort' => 12,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'label_storage_bags',
                'title_ru' => 'Мешков одежды',
                'title_uz' => 'Kiyim qoplari',
                'sort' => 13,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'label_inbound_boxes',
                'title_ru' => 'Коробов приёмки в месяц',
                'title_uz' => 'Oyiga qabul koroblari',
                'sort' => 14,
            ],
            
            // Output labels
            [
                'page_slug' => 'calculator',
                'block_key' => 'output_monthly_fee',
                'title_ru' => 'Абонплата',
                'title_uz' => 'Oylik to\'lov',
                'sort' => 20,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'output_included_limits',
                'title_ru' => 'Включённые лимиты',
                'title_uz' => 'Kiritilgan limitlar',
                'sort' => 21,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'output_overages',
                'title_ru' => 'Переплаты',
                'title_uz' => 'Ortiqcha to\'lovlar',
                'sort' => 22,
            ],
            [
                'page_slug' => 'calculator',
                'block_key' => 'output_total',
                'title_ru' => 'Итого',
                'title_uz' => 'Yakuniy summa',
                'sort' => 23,
            ],
            
            // Disclaimer
            [
                'page_slug' => 'calculator',
                'block_key' => 'disclaimer',
                'body_ru' => 'DBS и FBO считаются отдельно. Нестандарт — по согласованию.',
                'body_uz' => 'DBS va FBO alohida hisoblanadi. Nostandart — kelishuv asosida.',
                'sort' => 30,
            ],
        ];

        foreach ($blocks as $blockData) {
            ContentBlock::updateOrCreate(
                [
                    'page_slug' => $blockData['page_slug'],
                    'block_key' => $blockData['block_key'],
                ],
                $blockData
            );
        }
    }
}
