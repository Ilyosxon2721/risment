<?php

namespace Database\Seeders;

use App\Models\ServiceAddon;
use Illuminate\Database\Seeder;

class ServiceAddonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addons = [
            // === INBOUND (Приёмка) ===
            [
                'code' => 'ADDON_INBOUND_PHOTO',
                'scope' => 'inbound',
                'title_ru' => 'Фотоотчёт при приёмке',
                'title_uz' => 'Qabul qilishda foto hisobot',
                'unit_ru' => 'за фото',
                'unit_uz' => 'har bir foto uchun',
                'pricing_type' => 'fixed',
                'value' => 500,
                'description_ru' => 'Фотофиксация товара при приёмке на склад',
                'description_uz' => 'Tovarni omborga qabul qilishda suratga olish',
                'sort' => 10,
            ],
            [
                'code' => 'ADDON_INBOUND_RECOUNT',
                'scope' => 'inbound',
                'title_ru' => 'Пересчёт товаров',
                'title_uz' => 'Tovarlarni qayta hisoblash',
                'unit_ru' => 'за единицу',
                'unit_uz' => 'har bir dona uchun',
                'pricing_type' => 'fixed',
                'value' => 200,
                'description_ru' => 'Дополнительный пересчёт при расхождении количества',
                'description_uz' => 'Miqdor mos kelmaganda qo\'shimcha hisoblash',
                'sort' => 20,
            ],
            [
                'code' => 'ADDON_INBOUND_LABELING',
                'scope' => 'inbound',
                'title_ru' => 'Маркировка товаров',
                'title_uz' => 'Tovarlarni belgilash',
                'unit_ru' => 'за единицу',
                'unit_uz' => 'har bir dona uchun',
                'pricing_type' => 'fixed',
                'value' => 300,
                'description_ru' => 'Наклейка штрих-кодов и этикеток',
                'description_uz' => 'Shtrix-kod va etiketka yopish',
                'sort' => 30,
            ],

            // === PICKPACK (Сборка) ===
            [
                'code' => 'ADDON_PP_PROTECT',
                'scope' => 'pickpack',
                'title_ru' => 'Защитная упаковка',
                'title_uz' => 'Himoya qadoqlash',
                'unit_ru' => 'за единицу',
                'unit_uz' => 'har bir dona uchun',
                'pricing_type' => 'by_category',
                'value' => null,
                'meta' => [
                    'MICRO' => 200,
                    'MGT' => 300,
                    'SGT' => 500,
                    'KGT' => 800,
                ],
                'description_ru' => 'Дополнительная защитная упаковка (пузырчатая плёнка)',
                'description_uz' => 'Qo\'shimcha himoya qadoqlash (pufakli plyonka)',
                'sort' => 10,
            ],
            [
                'code' => 'ADDON_PP_FRAGILE',
                'scope' => 'pickpack',
                'title_ru' => 'Упаковка хрупкого товара',
                'title_uz' => 'Mo\'rt tovarni qadoqlash',
                'unit_ru' => 'за единицу',
                'unit_uz' => 'har bir dona uchun',
                'pricing_type' => 'by_category',
                'value' => null,
                'meta' => [
                    'MICRO' => 500,
                    'MGT' => 800,
                    'SGT' => 1500,
                    'KGT' => 2500,
                ],
                'description_ru' => 'Усиленная упаковка для хрупких товаров',
                'description_uz' => 'Mo\'rt tovarlar uchun kuchaytirilgan qadoqlash',
                'sort' => 20,
            ],
            [
                'code' => 'ADDON_PP_GIFT',
                'scope' => 'pickpack',
                'title_ru' => 'Подарочная упаковка',
                'title_uz' => 'Sovg\'a qadoqlash',
                'unit_ru' => 'за заказ',
                'unit_uz' => 'har bir buyurtma uchun',
                'pricing_type' => 'fixed',
                'value' => 2000,
                'description_ru' => 'Оформление в подарочную упаковку',
                'description_uz' => 'Sovg\'a qadoqlashda rasmiylashtirish',
                'sort' => 30,
            ],
            [
                'code' => 'ADDON_PP_INSERT',
                'scope' => 'pickpack',
                'title_ru' => 'Вложение материалов',
                'title_uz' => 'Materiallarni qo\'shish',
                'unit_ru' => 'за вложение',
                'unit_uz' => 'har bir qo\'shish uchun',
                'pricing_type' => 'fixed',
                'value' => 200,
                'description_ru' => 'Вложение рекламных материалов, визиток',
                'description_uz' => 'Reklama materiallari, tashrif qog\'ozlarini qo\'shish',
                'sort' => 40,
            ],

            // === STORAGE (Хранение) ===
            [
                'code' => 'ADDON_STORAGE_PALLET',
                'scope' => 'storage',
                'title_ru' => 'Хранение на паллете',
                'title_uz' => 'Palletda saqlash',
                'unit_ru' => 'за паллето-день',
                'unit_uz' => 'har bir pallet-kun uchun',
                'pricing_type' => 'fixed',
                'value' => 15000,
                'description_ru' => 'Хранение крупногабаритных товаров на паллетах',
                'description_uz' => 'Katta o\'lchamli tovarlarni palletlarda saqlash',
                'sort' => 10,
            ],
            [
                'code' => 'ADDON_STORAGE_CLIMATE',
                'scope' => 'storage',
                'title_ru' => 'Климат-контроль',
                'title_uz' => 'Iqlim nazorati',
                'unit_ru' => 'за коробко-день',
                'unit_uz' => 'har bir quti-kun uchun',
                'pricing_type' => 'fixed',
                'value' => 500,
                'description_ru' => 'Хранение в зоне с контролем температуры',
                'description_uz' => 'Harorat nazorati zonasida saqlash',
                'sort' => 20,
            ],

            // === SHIPPING (Отправка) ===
            [
                'code' => 'ADDON_SHIP_INSURANCE',
                'scope' => 'shipping',
                'title_ru' => 'Страхование отправления',
                'title_uz' => 'Jo\'natmani sug\'urtalash',
                'unit_ru' => '% от стоимости',
                'unit_uz' => 'qiymatdan %',
                'pricing_type' => 'percent',
                'value' => 0.02, // 2%
                'description_ru' => 'Страхование груза на полную стоимость',
                'description_uz' => 'Yukni to\'liq qiymatga sug\'urtalash',
                'sort' => 10,
            ],
            [
                'code' => 'ADDON_SHIP_COD',
                'scope' => 'shipping',
                'title_ru' => 'Наложенный платёж',
                'title_uz' => 'Yetkazib berishda to\'lov',
                'unit_ru' => '% от суммы',
                'unit_uz' => 'summadan %',
                'pricing_type' => 'percent',
                'value' => 0.03, // 3%
                'description_ru' => 'Услуга приёма оплаты при доставке',
                'description_uz' => 'Yetkazib berishda to\'lovni qabul qilish xizmati',
                'sort' => 20,
            ],
            [
                'code' => 'ADDON_SHIP_EXPRESS',
                'scope' => 'shipping',
                'title_ru' => 'Экспресс-доставка',
                'title_uz' => 'Ekspress yetkazib berish',
                'unit_ru' => 'за отправку',
                'unit_uz' => 'har bir jo\'natma uchun',
                'pricing_type' => 'by_category',
                'value' => null,
                'meta' => [
                    'MICRO' => 5000,
                    'MGT' => 8000,
                    'SGT' => 15000,
                    'KGT' => 25000,
                ],
                'description_ru' => 'Доставка в течение 24 часов',
                'description_uz' => '24 soat ichida yetkazib berish',
                'sort' => 30,
            ],

            // === RETURNS (Возвраты) ===
            [
                'code' => 'ADDON_RETURN_INSPECT',
                'scope' => 'returns',
                'title_ru' => 'Проверка возврата',
                'title_uz' => 'Qaytarilgan tovarni tekshirish',
                'unit_ru' => 'за единицу',
                'unit_uz' => 'har bir dona uchun',
                'pricing_type' => 'by_category',
                'value' => null,
                'meta' => [
                    'MICRO' => 300,
                    'MGT' => 500,
                    'SGT' => 800,
                    'KGT' => 1200,
                ],
                'description_ru' => 'Проверка состояния возвращённого товара',
                'description_uz' => 'Qaytarilgan tovar holatini tekshirish',
                'sort' => 10,
            ],
            [
                'code' => 'ADDON_RETURN_REPACK',
                'scope' => 'returns',
                'title_ru' => 'Переупаковка возврата',
                'title_uz' => 'Qaytarilgan tovarni qayta qadoqlash',
                'unit_ru' => 'за единицу',
                'unit_uz' => 'har bir dona uchun',
                'pricing_type' => 'by_category',
                'value' => null,
                'meta' => [
                    'MICRO' => 200,
                    'MGT' => 400,
                    'SGT' => 700,
                    'KGT' => 1000,
                ],
                'description_ru' => 'Переупаковка товара для повторной продажи',
                'description_uz' => 'Tovarni qayta sotish uchun qayta qadoqlash',
                'sort' => 20,
            ],

            // === OTHER (Другое) ===
            [
                'code' => 'ADDON_PHOTO_PRODUCT',
                'scope' => 'other',
                'title_ru' => 'Фотосъёмка товара',
                'title_uz' => 'Tovarni suratga olish',
                'unit_ru' => 'за товар',
                'unit_uz' => 'har bir tovar uchun',
                'pricing_type' => 'fixed',
                'value' => 5000,
                'description_ru' => 'Профессиональная фотосъёмка для каталога',
                'description_uz' => 'Katalog uchun professional suratga olish',
                'sort' => 10,
            ],
            [
                'code' => 'ADDON_CUSTOM_WORK',
                'scope' => 'other',
                'title_ru' => 'Дополнительные работы',
                'title_uz' => 'Qo\'shimcha ishlar',
                'unit_ru' => 'по согласованию',
                'unit_uz' => 'kelishilgan narxda',
                'pricing_type' => 'manual',
                'value' => null,
                'description_ru' => 'Индивидуальные работы по запросу клиента',
                'description_uz' => 'Mijoz so\'roviga ko\'ra individual ishlar',
                'sort' => 100,
            ],
        ];

        foreach ($addons as $addonData) {
            ServiceAddon::updateOrCreate(
                ['code' => $addonData['code']],
                $addonData
            );
        }

        $this->command->info('Service addons seeded: ' . count($addons) . ' items');
    }
}
