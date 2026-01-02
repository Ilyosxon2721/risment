<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SizeCategory;
use App\Models\TariffPlan;
use App\Models\TariffCategory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin role (skip if already exists)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Create admin user (skip if already exists)
        $admin = User::firstOrCreate(
            ['email' => 'admin@risment.uz'],
            [
                'name' => 'Admin',
                'phone' => '+998901234567',
                'password' => bcrypt('admin123'),
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);
        
        // Seed subscription plans
        $this->call(SubscriptionPlanSeeder::class);
        
        // Create size categories (MGT/SGT/KGT) with prices
        SizeCategory::firstOrCreate(
            ['code' => 'mgt'],
            ['sum_min' => 0, 'sum_max' => 60, 'price' => 5000.00, 'is_active' => true]
        );
        
        SizeCategory::firstOrCreate(
            ['code' => 'sgt'],
            ['sum_min' => 61, 'sum_max' => 170, 'price' => 8000.00, 'is_active' => true]
        );
        
        SizeCategory::firstOrCreate(
            ['code' => 'kgt'],
            ['sum_min' => 171, 'sum_max' => null, 'price' => 20000.00, 'is_active' => true]
        );
        
        // Create default tariff plan
        TariffPlan::firstOrCreate(
            ['name' => 'Standart'],
            ['description' => 'Default pricing plan for RISMENT services', 'is_default' => true, 'is_active' => true]
        );
        
        // Create tariff categories
        $categories = [
            ['code' => 'onboarding', 'title_ru' => 'Онбординг и управление', 'title_uz' => 'Onbording va boshqaruv'],
            ['code' => 'inbound', 'title_ru' => 'Приёмка', 'title_uz' => 'Qabul qilish'],
            ['code' => 'storage', 'title_ru' => 'Хранение', 'title_uz' => 'Saqlash'],
            ['code' => 'packing', 'title_ru' => 'Упаковочные материалы', 'title_uz' => 'Qadoqlash materiallari'],
            ['code' => 'pickpack', 'title_ru' => 'Pick & Pack', 'title_uz' => 'Pick & Pack'],
            ['code' => 'logistics', 'title_ru' => 'Логистика', 'title_uz' => 'Logistika'],
            ['code' => 'fbo_shipping', 'title_ru' => 'Доставка FBO', 'title_uz' => 'FBO yetkazish'],
            ['code' => 'reverse', 'title_ru' => 'Возвраты', 'title_uz' => 'Qaytarishlar'],
            ['code' => 'extras', 'title_ru' => 'Дополнительно', 'title_uz' => 'Qo\'shimcha'],
        ];
        
        foreach ($categories as $index => $cat) {
            TariffCategory::firstOrCreate(
                ['code' => $cat['code']],
                array_merge($cat, ['sort' => $index * 10])
            );
        }
    }
}
