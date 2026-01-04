<?php

namespace Database\Seeders;

use App\Models\PricingRate;
use Illuminate\Database\Seeder;

class PricingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            // Pick & Pack rates by category
            [
                'code' => 'PICKPACK_MGT_FIRST',
                'value' => 4000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Pick&Pack первой позиции MGT (≤60см)',
                'description_uz' => 'MGT (≤60sm) birinchi pozitsiyani Pick&Pack',
            ],
            [
                'code' => 'PICKPACK_MGT_ADD',
                'value' => 1000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Pick&Pack дополнительной позиции MGT (в том же заказе)',
                'description_uz' => 'MGT qo\'shimcha pozitsiyani Pick&Pack (bir buyurtmada)',
            ],
            [
                'code' => 'PICKPACK_SGT_FIRST',
                'value' => 7000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Pick&Pack первой позиции SGT (61-120см)',
                'description_uz' => 'SGT (61-120sm) birinchi pozitsiyani Pick&Pack',
            ],
            [
                'code' => 'PICKPACK_SGT_ADD',
                'value' => 3000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Pick&Pack дополнительной позиции SGT (в том же заказе)',
                'description_uz' => 'SGT qo\'shimcha pozitsiyani Pick&Pack (bir buyurtmada)',
            ],
            [
                'code' => 'PICKPACK_KGT_FIRST',
                'value' => 15000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Pick&Pack первой позиции KGT (>120см)',
                'description_uz' => 'KGT (>120sm) birinchi pozitsiyani Pick&Pack',
            ],
            [
                'code' => 'PICKPACK_KGT_ADD',
                'value' => 10000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Pick&Pack дополнительной позиции KGT (в том же заказе)',
                'description_uz' => 'KGT qo\'shimcha pozitsiyani Pick&Pack (bir buyurtmada)',
            ],
            
            // Delivery (Довоз до точки сдачи) rates by category
            [
                'code' => 'DELIVERY_MGT',
                'value' => 4000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Довоз до точки сдачи MGT (≤60см)',
                'description_uz' => 'MGT (≤60sm) topshirish nuqtasigacha yetkazish',
            ],
            [
                'code' => 'DELIVERY_SGT',
                'value' => 8000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Довоз до точки сдачи SGT (61-120см)',
                'description_uz' => 'SGT (61-120sm) topshirish nuqtasigacha yetkazish',
            ],
            [
                'code' => 'DELIVERY_KGT',
                'value' => 20000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Довоз до точки сдачи KGT (>120см)',
                'description_uz' => 'KGT (>120sm) topshirish nuqtasigacha yetkazish',
            ],
            
            // Storage rates (per day)
            [
                'code' => 'STORAGE_BOX_DAY',
                'value' => 350,
                'unit_ru' => 'сум/день',
                'unit_uz' => 'so\'m/kun',
                'description_ru' => 'Хранение короба 60×40×40 см в день',
                'description_uz' => '60×40×40 sm qutini saqlash (kunlik)',
            ],
            [
                'code' => 'STORAGE_BAG_DAY',
                'value' => 500,
                'unit_ru' => 'сум/день',
                'unit_uz' => 'so\'m/kun',
                'description_ru' => 'Хранение мешка одежды в день',
                'description_uz' => 'Kiyim xaltasini saqlash (kunlik)',
            ],
            [
                'code' => 'STORAGE_PALLET_DAY',
                'value' => 4000,
                'unit_ru' => 'сум/день',
                'unit_uz' => 'so\'m/kun',
                'description_ru' => 'Хранение паллеты в день',
                'description_uz' => 'Palletni saqlash (kunlik)',
            ],
            [
                'code' => 'STORAGE_M3_DAY',
                'value' => 7000,
                'unit_ru' => 'сум/день',
                'unit_uz' => 'so\'m/kun',
                'description_ru' => 'Хранение кубического метра в день',
                'description_uz' => 'Kub metrni saqlash (kunlik)',
            ],
            
            // Inbound rates
            [
                'code' => 'INBOUND_BOX',
                'value' => 15000,
                'unit_ru' => 'сум за короб',
                'unit_uz' => 'so\'m har bir quti uchun',
                'description_ru' => 'Приёмка короба (включает до 50 позиций)',
                'description_uz' => 'Qutini qabul qilish (50 tagacha pozitsiya)',
            ],
            [
                'code' => 'INBOUND_BOX_INCLUDED_ITEMS',
                'value' => 50,
                'unit_ru' => 'позиций',
                'unit_uz' => 'pozitsiya',
                'description_ru' => 'Количество позиций, включенных в стоимость короба',
                'description_uz' => 'Quti narxiga kiritilgan pozitsiyalar soni',
            ],
            [
                'code' => 'INBOUND_BOX_EXTRA_ITEM',
                'value' => 150,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Доплата за позицию сверх 50 в коробе',
                'description_uz' => 'Qutidagi 50 dan ortiq pozitsiya uchun qo\'shimcha to\'lov',
            ],
            [
                'code' => 'INBOUND_ITEM',
                'value' => 400,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Приёмка по позициям (без короба)',
                'description_uz' => 'Pozitsiyalar bo\'yicha qabul qilish (qutisiz)',
            ],
            
            // Returns rates
            [
                'code' => 'RETURN_INTAKE',
                'value' => 1000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Приёмка возврата',
                'description_uz' => 'Qaytarilgan mahsulotni qabul qilish',
            ],
            [
                'code' => 'RETURN_CHECK_MGT',
                'value' => 2000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'QA проверка возврата MGT',
                'description_uz' => 'MGT qaytarish QA tekshiruvi',
            ],
            [
                'code' => 'RETURN_CHECK_SGT',
                'value' => 3000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'QA проверка возврата SGT',
                'description_uz' => 'SGT qaytarish QA tekshiruvi',
            ],
            [
                'code' => 'RETURN_CHECK_KGT',
                'value' => 5000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'QA проверка возврата KGT',
                'description_uz' => 'KGT qaytarish QA tekshiruvi',
            ],
            [
                'code' => 'RETURN_PHOTO',
                'value' => 2000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Фото возврата',
                'description_uz' => 'Qaytarilgan mahsulot fotosi',
            ],
            [
                'code' => 'RETURN_RESTOCK',
                'value' => 1000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Возврат на продажу (restock)',
                'description_uz' => 'Sotuvga qaytarish (restock)',
            ],
            
            // Photo services
            [
                'code' => 'INBOUND_PHOTO',
                'value' => 2000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Фото при приёмке',
                'description_uz' => 'Qabul qilishda fotoga olish',
            ],
            [
                'code' => 'INBOUND_DAMAGE_PHOTO',
                'value' => 3000,
                'unit_ru' => 'сум за позицию',
                'unit_uz' => 'so\'m har bir pozitsiya uchun',
                'description_ru' => 'Фото повреждений при приёмке',
                'description_uz' => 'Qabul qilishda shikastlanish fotosi',
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
