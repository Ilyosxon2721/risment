<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Combined seeder for all pricing data:
 * - Base rates (pricing_rates table)
 * - Service add-ons (service_addons table)
 *
 * Run: php artisan db:seed --class=RismentPricingSeeder
 */
class RismentPricingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RismentRatesSeeder::class,
            ServiceAddonsSeeder::class,
        ]);
    }
}
