<?php

namespace Database\Seeders;

use App\Models\PricingRate;
use Illuminate\Database\Seeder;

class ManagerTaskRatesSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            // Additional services for manager tasks
            [
                'code' => 'PACKAGING_UNIT',
                'value' => 5000,
                'unit_ru' => 'шт',
                'unit_uz' => 'dona',
                'description_ru' => 'Упаковка товаров — за единицу',
                'description_uz' => 'Tovarlarni qadoqlash — bir dona uchun',
            ],
            [
                'code' => 'LABELING_UNIT',
                'value' => 3000,
                'unit_ru' => 'шт',
                'unit_uz' => 'dona',
                'description_ru' => 'Маркировка товаров — за единицу',
                'description_uz' => 'Tovarlarni markalash — bir dona uchun',
            ],
            [
                'code' => 'PHOTO_UNIT',
                'value' => 15000,
                'unit_ru' => 'шт',
                'unit_uz' => 'dona',
                'description_ru' => 'Фотосъёмка товаров — за единицу',
                'description_uz' => 'Tovarlarni suratga olish — bir dona uchun',
            ],
            [
                'code' => 'INVENTORY_CHECK',
                'value' => 2000,
                'unit_ru' => 'шт',
                'unit_uz' => 'dona',
                'description_ru' => 'Инвентаризация — за единицу',
                'description_uz' => 'Inventarizatsiya — bir dona uchun',
            ],
        ];

        foreach ($rates as $rate) {
            PricingRate::firstOrCreate(
                ['code' => $rate['code']],
                array_merge($rate, ['is_active' => true])
            );
        }

        $this->command->info('Manager task rates seeded successfully!');
    }
}
