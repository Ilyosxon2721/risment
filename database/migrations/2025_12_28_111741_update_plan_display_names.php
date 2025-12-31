<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update plan display names with proper localization
        DB::table('subscription_plans')->where('code', 'lite')->update([
            'name_ru' => 'Стартовый',
            'name_uz' => "Boshlang'ich",
        ]);

        DB::table('subscription_plans')->where('code', 'start')->update([
            'name_ru' => 'Стандарт',
            'name_uz' => 'Standart',
        ]);

        DB::table('subscription_plans')->where('code', 'pro')->update([
            'name_ru' => 'Рост',
            'name_uz' => 'Rivoj',
        ]);

        DB::table('subscription_plans')->where('code', 'business')->update([
            'name_ru' => 'Корпоративный',
            'name_uz' => 'Korporativ',
        ]);

        DB::table('subscription_plans')->where('code', 'enterprise')->update([
            'name_ru' => 'Индивидуальный',
            'name_uz' => 'Maxsus',
        ]);
    }

    public function down(): void
    {
        // Rollback to old code-based names
        DB::table('subscription_plans')->where('code', 'lite')->update([
            'name_ru' => 'LITE',
            'name_uz' => 'LITE',
        ]);

        DB::table('subscription_plans')->where('code', 'start')->update([
            'name_ru' => 'START',
            'name_uz' => 'START',
        ]);

        DB::table('subscription_plans')->where('code', 'pro')->update([
            'name_ru' => 'PRO',
            'name_uz' => 'PRO',
        ]);

        DB::table('subscription_plans')->where('code', 'business')->update([
            'name_ru' => 'BUSINESS',
            'name_uz' => 'BUSINESS',
        ]);

        DB::table('subscription_plans')->where('code', 'enterprise')->update([
            'name_ru' => 'ENTERPRISE',
            'name_uz' => 'ENTERPRISE',
        ]);
    }
};
