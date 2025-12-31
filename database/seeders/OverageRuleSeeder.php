<?php

namespace Database\Seeders;

use App\Models\OverageRule;
use Illuminate\Database\Seeder;

class OverageRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'code' => 'PLAN_SHIPMENT_OVERAGE',
                'scope' => 'plan',
                'type' => 'shipments',
                'pricing_mode' => 'per_unit_base',
                'fee_mgt' => null,
                'fee_sgt' => null,
                'fee_kgt' => null,
                'fee' => null,
            ],
            [
                'code' => 'PLAN_STORAGE_BOX_OVERAGE',
                'scope' => 'plan',
                'type' => 'storage_boxes',
                'pricing_mode' => 'fixed',
                'fee_mgt' => null,
                'fee_sgt' => null,
                'fee_kgt' => null,
                'fee' => 18000,
            ],
            [
                'code' => 'PLAN_STORAGE_BAG_OVERAGE',
                'scope' => 'plan',
                'type' => 'storage_bags',
                'pricing_mode' => 'fixed',
                'fee_mgt' => null,
                'fee_sgt' => null,
                'fee_kgt' => null,
                'fee' => 12000,
            ],
            [
                'code' => 'PLAN_INBOUND_BOX_OVERAGE',
                'scope' => 'plan',
                'type' => 'inbound_boxes',
                'pricing_mode' => 'fixed',
                'fee_mgt' => null,
                'fee_sgt' => null,
                'fee_kgt' => null,
                'fee' => 15000,
            ],
        ];

        foreach ($rules as $ruleData) {
            OverageRule::updateOrCreate(
                ['code' => $ruleData['code']],
                $ruleData
            );
        }

        $this->command->info('Overage rules seeded successfully!');
    }
}
