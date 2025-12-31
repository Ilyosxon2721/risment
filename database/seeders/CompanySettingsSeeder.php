<?php

namespace Database\Seeders;

use App\Models\CompanySettings;
use Illuminate\Database\Seeder;

class CompanySettingsSeeder extends Seeder
{
    public function run(): void
    {
        CompanySettings::firstOrCreate([], [
            'company_name' => 'RISMENT',
            'phone' => '+998 (90) 123-45-67',
            'email' => 'info@risment.uz',
            'address_ru' => 'г. Ташкент, Юнусабадский район',
            'address_uz' => 'Toshkent sh., Yunusobod tumani',
            'warehouse_address_ru' => 'г. Ташкент, Юнусабадский район',
            'warehouse_address_uz' => 'Toshkent sh., Yunusobod tumani',
            'stat_orders' => '10 000+',
            'stat_sla' => '99%',
            'stat_support' => '24/7',
            'stat_warehouse_size' => '5 000+',
        ]);
    }
}
