<?php

namespace Database\Seeders;

use App\Models\SurchargeTier;
use Illuminate\Database\Seeder;

class SurchargeTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'min_shipments' => 0,
                'max_shipments' => 50,
                'surcharge_percent' => 0.00,
                'sort' => 1,
            ],
            [
                'min_shipments' => 51,
                'max_shipments' => 300,
                'surcharge_percent' => 10.00,
                'sort' => 2,
            ],
            [
                'min_shipments' => 301,
                'max_shipments' => null, // unlimited
                'surcharge_percent' => 20.00,
                'sort' => 3,
            ],
        ];

        foreach ($tiers as $tierData) {
            SurchargeTier::create($tierData);
        }

        $this->command->info('Surcharge tiers seeded successfully!');
    }
}
