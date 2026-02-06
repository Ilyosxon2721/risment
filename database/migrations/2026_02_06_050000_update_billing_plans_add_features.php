<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_plans', function (Blueprint $table) {
            // Plan descriptions
            $table->text('description_ru')->nullable()->after('name_uz');
            $table->text('description_uz')->nullable()->after('description_ru');

            // Features list (JSON array of feature strings)
            $table->json('features_ru')->nullable()->after('description_uz');
            $table->json('features_uz')->nullable()->after('features_ru');

            // Discount percentage for bulk operations
            $table->integer('discount_percent')->default(0)->after('features_uz');

            // Min/max order limits per month
            $table->integer('min_orders_month')->default(0)->after('discount_percent');
            $table->integer('max_orders_month')->nullable()->after('min_orders_month');

            // Storage limits
            $table->integer('max_storage_units')->nullable()->after('max_orders_month');

            // Free storage days for new items
            $table->integer('free_storage_days')->default(0)->after('max_storage_units');

            // Free return storage days
            $table->integer('free_return_days')->default(10)->after('free_storage_days');

            // Badge/highlight for UI
            $table->string('badge', 50)->nullable()->after('free_return_days');
            $table->boolean('is_popular')->default(false)->after('badge');
            $table->boolean('is_visible')->default(true)->after('is_popular');
        });
    }

    public function down(): void
    {
        Schema::table('billing_plans', function (Blueprint $table) {
            $table->dropColumn([
                'description_ru',
                'description_uz',
                'features_ru',
                'features_uz',
                'discount_percent',
                'min_orders_month',
                'max_orders_month',
                'max_storage_units',
                'free_storage_days',
                'free_return_days',
                'badge',
                'is_popular',
                'is_visible',
            ]);
        });
    }
};
