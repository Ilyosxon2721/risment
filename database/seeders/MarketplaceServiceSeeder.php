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
            
            // Management services
            [
                'code' => 'management_uzum',
                'service_group' => 'management',
                'marketplace' => 'uzum',
                'name_ru' => 'Управление на Uzum',
                'name_uz' => 'Uzumda boshqaruv',
                'description_ru' => 'Профессиональное ведение маркетплейса',
                'description_uz' => 'Professional marketpleys boshqaruvi',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 1790000,
                'sku_limit' => 100,
                'sort' => 10,
            ],
            [
                'code' => 'management_wb',
                'service_group' => 'management',
                'marketplace' => 'wildberries',
                'name_ru' => 'Управление на Wildberries',
                'name_uz' => 'Wildberriesda boshqaruv',
                'description_ru' => 'Профессиональное ведение маркетплейса',
                'description_uz' => 'Professional marketpleys boshqaruvi',
                'unit_ru' => 'в месяц',
                'unit_uz' => 'oyiga',
                'price' => 1790000,
                'sku_limit' => 100,
                'sort' => 11,
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
