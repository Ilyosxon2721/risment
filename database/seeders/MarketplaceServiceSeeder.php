<?php

namespace Database\Seeders;

use App\Models\MarketplaceService;
use Illuminate\Database\Seeder;

class MarketplaceServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Launch services
            [
                'code' => 'launch_uzum',
                'service_group' => 'launch',
                'marketplace' => 'uzum',
                'name_ru' => 'Запуск на Uzum',
                'name_uz' => 'Uzumda ishga tushirish',
                'description_ru' => 'Настройка аккаунта и загрузка каталога',
                'description_uz' => 'Akkaunt sozlash va katalog yuklash',
                'unit_ru' => 'разово',
                'unit_uz' => 'bir marta',
                'price' => 1900000,
                'sku_limit' => 50,
                'sort' => 1,
            ],
            [
                'code' => 'launch_wb',
                'service_group' => 'launch',
                'marketplace' => 'wildberries',
                'name_ru' => 'Запуск на Wildberries',
                'name_uz' => 'Wildberriesda ishga tushirish',
                'description_ru' => 'Настройка аккаунта и загрузка каталога',
                'description_uz' => 'Akkaunt sozlash va katalog yuklash',
                'unit_ru' => 'разово',
                'unit_uz' => 'bir marta',
                'price' => 1900000,
                'sku_limit' => 50,
                'sort' => 2,
            ],
            
            // Management services - Uzum packages
            [
                'code' => 'MGMT_UZUM_100',
                'service_group' => 'management',
                'marketplace' => 'uzum',
                'name_ru' => 'Управление на Uzum до 100 SKU',
                'name_uz' => 'Uzumda 100 SKU gacha boshqaruv',
                'description_ru' => 'Профессиональное ведение маркетплейса',
                'description_uz' => 'Professional marketpleys boshqaruvi',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 1790000,
                'sku_limit' => 100,
                'sort' => 10,
            ],
            [
                'code' => 'MGMT_UZUM_200',
                'service_group' => 'management',
                'marketplace' => 'uzum',
                'name_ru' => 'Управление на Uzum до 200 SKU',
                'name_uz' => 'Uzumda 200 SKU gacha boshqaruv',
                'description_ru' => 'Профессиональное ведение маркетплейса',
                'description_uz' => 'Professional marketpleys boshqaruvi',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 2990000,
                'sku_limit' => 200,
                'sort' => 11,
            ],
            [
                'code' => 'MGMT_UZUM_CUSTOM',
                'service_group' => 'management',
                'marketplace' => 'uzum',
                'name_ru' => 'Управление на Uzum от 200 SKU',
                'name_uz' => 'Uzumda 200 SKU dan ortiq boshqaruv',
                'description_ru' => 'Индивидуальные условия',
                'description_uz' => 'Individual shartlar',
                'unit_ru' => 'индивидуально',
                'unit_uz' => 'individual',
                'price' => 0,
                'sku_limit' => null,
                'sort' => 12,
            ],
            
            // Management services - Complex marketplaces (WB, Ozon, Yandex)
            [
                'code' => 'MGMT_COMPLEX_60',
                'service_group' => 'management',
                'marketplace' => 'all',
                'name_ru' => 'Управление WB/Ozon/Yandex до 60 SKU',
                'name_uz' => 'WB/Ozon/Yandex 60 SKU gacha boshqaruv',
                'description_ru' => 'Профессиональное ведение маркетплейса',
                'description_uz' => 'Professional marketpleys boshqaruvi',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 1990000,
                'sku_limit' => 60,
                'sort' => 20,
            ],
            [
                'code' => 'MGMT_COMPLEX_100',
                'service_group' => 'management',
                'marketplace' => 'all',
                'name_ru' => 'Управление WB/Ozon/Yandex до 100 SKU',
                'name_uz' => 'WB/Ozon/Yandex 100 SKU gacha boshqaruv',
                'description_ru' => 'Профессиональное ведение маркетплейса',
                'description_uz' => 'Professional marketpleys boshqaruvi',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 3390000,
                'sku_limit' => 100,
                'sort' => 21,
            ],
            [
                'code' => 'MGMT_COMPLEX_CUSTOM',
                'service_group' => 'management',
                'marketplace' => 'all',
                'name_ru' => 'Управление WB/Ozon/Yandex от 100 SKU',
                'name_uz' => 'WB/Ozon/Yandex 100 SKU dan ortiq boshqaruv',
                'description_ru' => 'Индивидуальные условия',
                'description_uz' => 'Individual shartlar',
                'unit_ru' => 'индивидуально',
                'unit_uz' => 'individual',
                'price' => 0,
                'sku_limit' => null,
                'sort' => 22,
            ],
            
            // Ads addon
            [
                'code' => 'ADS_ADDON',
                'service_group' => 'ads_addon',
                'marketplace' => 'all',
                'name_ru' => 'Управление рекламой',
                'name_uz' => 'Reklama boshqaruvi',
                'description_ru' => 'Настройка и ведение рекламных кампаний. Рекламный бюджет оплачивается отдельно.',
                'description_uz' => 'Reklama kampaniyalarini sozlash va yuritish. Reklama byudjeti alohida to\'lanadi.',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 690000,
                'sku_limit' => null,
                'sort' => 30,
            ],
            
            // Infographics
            [
                'code' => 'infographics_premium',
                'service_group' => 'infographics',
                'marketplace' => 'all',
                'name_ru' => 'Инфографика Premium',
                'name_uz' => 'Premium infografika',
                'description_ru' => 'Создание продающих карточек',
                'description_uz' => 'Sotuvchi kartochkalarni yaratish',
                'unit_ru' => 'за товар',
                'unit_uz' => 'mahsulot uchun',
                'price' => 60000,
                'sort' => 20,
            ],
            [
                'code' => 'infographics_standard',
                'service_group' => 'infographics',
                'marketplace' => 'all',
                'name_ru' => 'Инфографика Standard',
                'name_uz' => 'Standart infografika',
                'description_ru' => 'Базовые карточки товаров',
                'description_uz' => 'Asosiy mahsulot kartochkalari',
                'unit_ru' => 'за товар',
                'unit_uz' => 'mahsulot uchun',
                'price' => 40000,
                'sort' => 21,
            ],
        ];

        foreach ($services as $service) {
            MarketplaceService::updateOrCreate(
                ['code' => $service['code']],
                $service
            );
        }
    }
}
