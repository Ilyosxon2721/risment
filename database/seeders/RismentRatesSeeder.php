<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RismentRatesSeeder extends Seeder
{
    /**
     * Seed all pricing rates.
     * Table: pricing_rates (code, value, unit_ru, unit_uz, description_ru, description_uz, is_active)
     */
    public function run(): void
    {
        $now = now();

        $rates = [
            // ================== PICK & PACK (per position) ==================
            [
                'code' => 'PICKPACK_MICRO_FIRST',
                'value' => 2000,
                'unit_ru' => 'сум/первая поз.',
                'unit_uz' => 'so\'m/birinchi poz.',
                'description_ru' => 'Сборка MICRO (≤30 см) — первая позиция в заказе',
                'description_uz' => 'MICRO yig\'ish (≤30 sm) — buyurtmadagi birinchi pozitsiya',
            ],
            [
                'code' => 'PICKPACK_MICRO_ADD',
                'value' => 1000,
                'unit_ru' => 'сум/доп. поз.',
                'unit_uz' => 'so\'m/qo\'sh. poz.',
                'description_ru' => 'Сборка MICRO — дополнительные позиции',
                'description_uz' => 'MICRO yig\'ish — qo\'shimcha pozitsiyalar',
            ],
            [
                'code' => 'PICKPACK_MGT_FIRST',
                'value' => 4000,
                'unit_ru' => 'сум/первая поз.',
                'unit_uz' => 'so\'m/birinchi poz.',
                'description_ru' => 'Сборка MGT (31-60 см) — первая позиция',
                'description_uz' => 'MGT yig\'ish (31-60 sm) — birinchi pozitsiya',
            ],
            [
                'code' => 'PICKPACK_MGT_ADD',
                'value' => 4000,
                'unit_ru' => 'сум/доп. поз.',
                'unit_uz' => 'so\'m/qo\'sh. poz.',
                'description_ru' => 'Сборка MGT — дополнительные позиции',
                'description_uz' => 'MGT yig\'ish — qo\'shimcha pozitsiyalar',
            ],
            [
                'code' => 'PICKPACK_SGT_FIRST',
                'value' => 7000,
                'unit_ru' => 'сум/первая поз.',
                'unit_uz' => 'so\'m/birinchi poz.',
                'description_ru' => 'Сборка SGT (61-120 см) — первая позиция',
                'description_uz' => 'SGT yig\'ish (61-120 sm) — birinchi pozitsiya',
            ],
            [
                'code' => 'PICKPACK_SGT_ADD',
                'value' => 4000,
                'unit_ru' => 'сум/доп. поз.',
                'unit_uz' => 'so\'m/qo\'sh. poz.',
                'description_ru' => 'Сборка SGT — дополнительные позиции',
                'description_uz' => 'SGT yig\'ish — qo\'shimcha pozitsiyalar',
            ],
            [
                'code' => 'PICKPACK_KGT_FIRST',
                'value' => 15000,
                'unit_ru' => 'сум/первая поз.',
                'unit_uz' => 'so\'m/birinchi poz.',
                'description_ru' => 'Сборка KGT (>120 см) — первая позиция',
                'description_uz' => 'KGT yig\'ish (>120 sm) — birinchi pozitsiya',
            ],
            [
                'code' => 'PICKPACK_KGT_ADD',
                'value' => 10000,
                'unit_ru' => 'сум/доп. поз.',
                'unit_uz' => 'so\'m/qo\'sh. poz.',
                'description_ru' => 'Сборка KGT — дополнительные позиции',
                'description_uz' => 'KGT yig\'ish — qo\'shimcha pozitsiyalar',
            ],

            // ================== DELIVERY (to PVZ) ==================
            [
                'code' => 'DELIVERY_MICRO',
                'value' => 2000,
                'unit_ru' => 'сум/отправка',
                'unit_uz' => 'so\'m/jo\'natma',
                'description_ru' => 'Доставка до ПВЗ — MICRO (≤30 см)',
                'description_uz' => 'PVZga yetkazish — MICRO (≤30 sm)',
            ],
            [
                'code' => 'DELIVERY_MGT',
                'value' => 4000,
                'unit_ru' => 'сум/отправка',
                'unit_uz' => 'so\'m/jo\'natma',
                'description_ru' => 'Доставка до ПВЗ — MGT (31-60 см)',
                'description_uz' => 'PVZga yetkazish — MGT (31-60 sm)',
            ],
            [
                'code' => 'DELIVERY_SGT',
                'value' => 8000,
                'unit_ru' => 'сум/отправка',
                'unit_uz' => 'so\'m/jo\'natma',
                'description_ru' => 'Доставка до ПВЗ — SGT (61-120 см)',
                'description_uz' => 'PVZga yetkazish — SGT (61-120 sm)',
            ],
            [
                'code' => 'DELIVERY_KGT',
                'value' => 20000,
                'unit_ru' => 'сум/отправка',
                'unit_uz' => 'so\'m/jo\'natma',
                'description_ru' => 'Доставка до ПВЗ — KGT (>120 см)',
                'description_uz' => 'PVZga yetkazish — KGT (>120 sm)',
            ],

            // ================== STORAGE (per day) ==================
            [
                'code' => 'STORAGE_BOX_DAY',
                'value' => 500,
                'unit_ru' => 'сум/коробко-день',
                'unit_uz' => 'so\'m/quti-kun',
                'description_ru' => 'Хранение товаров в коробках (~15 000 сум/месяц)',
                'description_uz' => 'Qutilarda tovar saqlash (~15 000 so\'m/oy)',
            ],
            [
                'code' => 'STORAGE_BAG_DAY',
                'value' => 350,
                'unit_ru' => 'сум/мешко-день',
                'unit_uz' => 'so\'m/xalta-kun',
                'description_ru' => 'Хранение товаров в мешках (~10 500 сум/месяц)',
                'description_uz' => 'Xaltalarda tovar saqlash (~10 500 so\'m/oy)',
            ],

            // ================== INBOUND ==================
            [
                'code' => 'INBOUND_BOX',
                'value' => 15000,
                'unit_ru' => 'сум/место',
                'unit_uz' => 'so\'m/joy',
                'description_ru' => 'Приёмка товаров на склад — за одно место',
                'description_uz' => 'Omborga tovar qabul qilish — bir joy uchun',
            ],
        ];

        foreach ($rates as $r) {
            DB::table('pricing_rates')->updateOrInsert(
                ['code' => $r['code']],
                [
                    'code' => $r['code'],
                    'value' => $r['value'],
                    'unit_ru' => $r['unit_ru'],
                    'unit_uz' => $r['unit_uz'],
                    'description_ru' => $r['description_ru'] ?? null,
                    'description_uz' => $r['description_uz'] ?? null,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $this->command->info('Pricing rates seeded: ' . count($rates) . ' items');
    }
}
