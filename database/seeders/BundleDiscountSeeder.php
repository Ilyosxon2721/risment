<?php

namespace Database\Seeders;

use App\Models\BundleDiscount;
use Illuminate\Database\Seeder;

class BundleDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            ['type' => 'management', 'marketplaces_count' => 2, 'discount_percent' => 7.00],
            ['type' => 'management', 'marketplaces_count' => 3, 'discount_percent' => 12.00],
            ['type' => 'management', 'marketplaces_count' => 4, 'discount_percent' => 18.00],
        ];

        foreach ($discounts as $discount) {
            BundleDiscount::updateOrCreate(
                [
                    'type' => $discount['type'],
                    'marketplaces_count' => $discount['marketplaces_count'],
                ],
                [
                    'discount_percent' => $discount['discount_percent'],
                    'is_active' => true,
                ]
            );
        }
    }
}
