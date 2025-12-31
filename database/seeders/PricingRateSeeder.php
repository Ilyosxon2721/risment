<?php

namespace Database\Seeders;

use App\Models\PricingRate;
use Illuminate\Database\Seeder;

class PricingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            [
                'code' => 'PICKPACK_UNIT',
                'value' => 7000,
                'unit_ru' => 'сум за отправку',
                'unit_uz' => 'so\'m har bir buyurtma uchun',
                'description_ru' => 'Стоимость сборки и упаковки одного заказа',
                'description_uz' => 'Bir buyurtmani yig\'ish va qadoqlash narxi',
            ],
            [
                'code' => 'DELIVERY_MGT',
                'value' => 4000,
                'unit_ru' => 'сум за отправку',
                'unit_uz' => 'so\'m har bir buyurtma uchun',
                'description_ru' => 'Доставка малогабаритной посылки (≤60см)',
                'description_uz' => 'Kichik hajmli pochta jo\'natmasi (≤60sm)',
            ],
            [
                'code' => 'DELIVERY_SGT',
                'value' => 8000,
                'unit_ru' => 'сум за отправку',
                'unit_uz' => 'so\'m har bir buyurtma uchun',
                'description_ru' => 'Доставка среднегабаритной посылки (61-120см)',
                'description_uz' => 'O\'rta hajmli pochta jo\'natmasi (61-120sm)',
            ],
            [
                'code' => 'DELIVERY_KGT',
                'value' => 20000,
                'unit_ru' => 'сум за отправку',
                'unit_uz' => 'so\'m har bir buyurtma uchun',
                'description_ru' => 'Доставка крупногабаритной посылки (>120см)',
                'description_uz' => 'Katta hajmli pochta jo\'natmasi (>120sm)',
            ],
            [
                'code' => 'STORAGE_BOX',
                'value' => 18000,
                'unit_ru' => 'сум/месяц за короб',
                'unit_uz' => 'so\'m/oy har bir quti uchun',
                'description_ru' => 'Хранение короба 60×40×40 см в месяц',
                'description_uz' => '60×40×40 sm qutini saqlash (oyiga)',
            ],
            [
                'code' => 'STORAGE_BAG',
                'value' => 12000,
                'unit_ru' => 'сум/месяц за мешок',
                'unit_uz' => 'so\'m/oy har bir xalta uchun',
                'description_ru' => 'Хранение мешка одежды в месяц',
                'description_uz' => 'Kiyim xaltasini saqlash (oyiga)',
            ],
            [
                'code' => 'INBOUND_BOX',
                'value' => 15000,
                'unit_ru' => 'сум за короб',
                'unit_uz' => 'so\'m har bir quti uchun',
                'description_ru' => 'Приёмка одного короба на склад',
                'description_uz' => 'Bir qutini qabul qilish',
            ],
        ];

        foreach ($rates as $rateData) {
            PricingRate::updateOrCreate(
                ['code' => $rateData['code']],
                $rateData
            );
        }

        $this->command->info('Pricing rates seeded successfully!');
    }
}
