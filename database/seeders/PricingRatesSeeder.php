<?php

namespace Database\Seeders;

use App\Models\PricingRate;
use Illuminate\Database\Seeder;

class PricingRatesSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            // MICRO (сумма сторон ≤ 30 см)
            [
                'code' => 'PICKPACK_MICRO_FIRST',
                'value' => 2000,
                'unit_ru' => 'сум / 1 позиция (MICRO)',
                'unit_uz' => "so'm / 1 pozitsiya (MICRO)",
                'description_ru' => 'Pick&Pack для MICRO (≤30 см). Включает маркировку/стикеры/штрихкод.',
                'description_uz' => "MICRO (≤30 sm) uchun Pick&Pack. Markalash/stiker/shtrix-kod kiradi.",
            ],
            [
                'code' => 'PICKPACK_MICRO_ADD',
                'value' => 1000,
                'unit_ru' => 'сум / доп. позиция (MICRO)',
                'unit_uz' => "so'm / qo'shimcha pozitsiya (MICRO)",
                'description_ru' => 'Дополнительная позиция Pick&Pack для MICRO',
                'description_uz' => "MICRO uchun qo'shimcha pozitsiya Pick&Pack",
            ],
            [
                'code' => 'DELIVERY_MICRO',
                'value' => 2000,
                'unit_ru' => 'сум / 1 позиция (MICRO)',
                'unit_uz' => "so'm / 1 pozitsiya (MICRO)",
                'description_ru' => 'Отгрузка до ПВЗ для MICRO',
                'description_uz' => 'MICRO uchun PVZ gacha yetkazib berish',
            ],

            // MGT (31–60 см)
            [
                'code' => 'PICKPACK_MGT_FIRST',
                'value' => 4000,
                'unit_ru' => 'сум / 1 позиция (MGT)',
                'unit_uz' => "so'm / 1 pozitsiya (MGT)",
                'description_ru' => 'Pick&Pack для MGT (31–60 см). Включает маркировку/стикеры/штрихкод.',
                'description_uz' => 'MGT (31–60 sm) uchun Pick&Pack. Markalash/stiker/shtrix-kod kiradi.',
            ],
            [
                'code' => 'PICKPACK_MGT_ADD',
                'value' => 1000,
                'unit_ru' => 'сум / доп. позиция (MGT)',
                'unit_uz' => "so'm / qo'shimcha pozitsiya (MGT)",
                'description_ru' => 'Дополнительная позиция Pick&Pack для MGT',
                'description_uz' => "MGT uchun qo'shimcha pozitsiya Pick&Pack",
            ],
            [
                'code' => 'DELIVERY_MGT',
                'value' => 4000,
                'unit_ru' => 'сум / 1 позиция (MGT)',
                'unit_uz' => "so'm / 1 pozitsiya (MGT)",
                'description_ru' => 'Отгрузка до ПВЗ для MGT',
                'description_uz' => 'MGT uchun PVZ gacha yetkazib berish',
            ],

            // SGT (61–120 см)
            [
                'code' => 'PICKPACK_SGT_FIRST',
                'value' => 7000,
                'unit_ru' => 'сум / 1 позиция (SGT)',
                'unit_uz' => "so'm / 1 pozitsiya (SGT)",
                'description_ru' => 'Pick&Pack для SGT (61–120 см). Включает маркировку/стикеры/штрихкод.',
                'description_uz' => 'SGT (61–120 sm) uchun Pick&Pack. Markalash/stiker/shtrix-kod kiradi.',
            ],
            [
                'code' => 'PICKPACK_SGT_ADD',
                'value' => 4000,
                'unit_ru' => 'сум / доп. позиция (SGT)',
                'unit_uz' => "so'm / qo'shimcha pozitsiya (SGT)",
                'description_ru' => 'Дополнительная позиция Pick&Pack для SGT',
                'description_uz' => "SGT uchun qo'shimcha pozitsiya Pick&Pack",
            ],
            [
                'code' => 'DELIVERY_SGT',
                'value' => 8000,
                'unit_ru' => 'сум / 1 позиция (SGT)',
                'unit_uz' => "so'm / 1 pozitsiya (SGT)",
                'description_ru' => 'Отгрузка до ПВЗ для SGT',
                'description_uz' => 'SGT uchun PVZ gacha yetkazib berish',
            ],

            // KGT (>120 см)
            [
                'code' => 'PICKPACK_KGT_FIRST',
                'value' => 15000,
                'unit_ru' => 'сум / 1 позиция (KGT)',
                'unit_uz' => "so'm / 1 pozitsiya (KGT)",
                'description_ru' => 'Pick&Pack для KGT (>120 см). Включает маркировку/стикеры/штрихкод.',
                'description_uz' => 'KGT (>120 sm) uchun Pick&Pack. Markalash/stiker/shtrix-kod kiradi.',
            ],
            [
                'code' => 'PICKPACK_KGT_ADD',
                'value' => 10000,
                'unit_ru' => 'сум / доп. позиция (KGT)',
                'unit_uz' => "so'm / qo'shimcha pozitsiya (KGT)",
                'description_ru' => 'Дополнительная позиция Pick&Pack для KGT',
                'description_uz' => "KGT uchun qo'shimcha pozitsiya Pick&Pack",
            ],
            [
                'code' => 'DELIVERY_KGT',
                'value' => 20000,
                'unit_ru' => 'сум / 1 позиция (KGT)',
                'unit_uz' => "so'm / 1 pozitsiya (KGT)",
                'description_ru' => 'Отгрузка до ПВЗ для KGT',
                'description_uz' => 'KGT uchun PVZ gacha yetkazib berish',
            ],

            // Хранение и приёмка
            [
                'code' => 'STORAGE_BOX_DAY',
                'value' => 600,
                'unit_ru' => 'сум / короб / день',
                'unit_uz' => "so'm / quti / kun",
                'description_ru' => 'Хранение товара в коробе (за день)',
                'description_uz' => 'Qutidagi tovarni saqlash (kuniga)',
            ],
            [
                'code' => 'STORAGE_BAG_DAY',
                'value' => 400,
                'unit_ru' => 'сум / мешок / день',
                'unit_uz' => "so'm / qop / kun",
                'description_ru' => 'Хранение товара в мешке (за день)',
                'description_uz' => 'Qopdagi tovarni saqlash (kuniga)',
            ],
            [
                'code' => 'INBOUND_BOX',
                'value' => 15000,
                'unit_ru' => 'сум / короб',
                'unit_uz' => "so'm / quti",
                'description_ru' => 'Приёмка товара (за короб)',
                'description_uz' => 'Tovar qabul qilish (quti uchun)',
            ],
        ];

        foreach ($rates as $rate) {
            PricingRate::updateOrCreate(
                ['code' => $rate['code']],
                array_merge($rate, ['is_active' => true])
            );
        }

        $this->command->info('Created/updated ' . count($rates) . ' pricing rates.');
    }
}
