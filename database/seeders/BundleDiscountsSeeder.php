<?php

namespace Database\Seeders;

use App\Models\BundleDiscount;
use Illuminate\Database\Seeder;

class BundleDiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'type' => 'management',
                'marketplaces_count' => 2,
                'discount_percent' => 7.00,
                'is_active' => true,
            ],
            [
                'type' => 'management',
                'marketplaces_count' => 3,
                'discount_percent' => 12.00,
                'is_active' => true,
            ],
            [
                'type' => 'management',
                'marketplaces_count' => 4,
                'discount_percent' => 18.00,
                'is_active' => true,
            ],
        ];

        foreach ($discounts as $discount) {
            BundleDiscount::updateOrCreate(
                [
                    'type' => $discount['type'],
                    'marketplaces_count' => $discount['marketplaces_count'],
                ],
                $discount
            );
        }

        $this->command->info('Bundle discounts seeded: 2 marketplaces (7%), 3 marketplaces (12%), 4 marketplaces (18%)');
    }
}
