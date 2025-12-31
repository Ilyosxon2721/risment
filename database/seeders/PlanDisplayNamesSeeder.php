<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class PlanDisplayNamesSeeder extends Seeder
{
    /**
     * Populate display names for subscription plans
     */
    public function run(): void
    {
        $displayNames = [
            'lite' => [
                'name_ru' => 'Стартовый',
                'name_uz' => "Boshlang'ich",
            ],
            'start' => [
                'name_ru' => 'Стандарт',
                'name_uz' => 'Standart',
            ],
            'pro' => [
                'name_ru' => 'Рост',
                'name_uz' => 'Rivoj',
            ],
            'business' => [
                'name_ru' => 'Корпоративный',
                'name_uz' => 'Korporativ',
            ],
            'enterprise' => [
                'name_ru' => 'Индивидуальный',
                'name_uz' => 'Maxsus',
            ],
        ];

        foreach ($displayNames as $code => $names) {
            $plan = SubscriptionPlan::where('code', $code)->first();
            
            if (!$plan) {
                $this->command->warn("Plan with code '{$code}' not found, skipping");
                continue;
            }

            $updated = false;

            // Only update if currently empty or NULL
            if (empty($plan->name_ru)) {
                $plan->name_ru = $names['name_ru'];
                $updated = true;
            }

            if (empty($plan->name_uz)) {
                $plan->name_uz = $names['name_uz'];
                $updated = true;
            }

            if ($updated) {
                $plan->save();
                $this->command->info("Updated display names for plan: {$code}");
            } else {
                $this->command->info("Display names already set for plan: {$code}");
            }
        }

        $this->command->info('Plan display names seeded successfully!');
    }
}
