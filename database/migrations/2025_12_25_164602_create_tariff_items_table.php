<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('tariff_plans')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('tariff_categories')->cascadeOnDelete();
            $table->enum('marketplace', ['uzum', 'wb', 'ozon', 'yandex', 'all'])->nullable();
            $table->enum('scheme', ['fbo', 'fbs', 'dbs', 'edbs', 'all'])->nullable();
            $table->string('name_ru');
            $table->string('name_uz');
            $table->enum('unit', ['шт', 'заказ', 'короб', 'сутки', 'месяц']);
            $table->enum('price_type', ['fixed', 'range'])->default('fixed');
            $table->decimal('price', 10, 2)->nullable(); // for fixed prices
            $table->integer('range_from')->nullable(); // for range-based prices
            $table->integer('range_to')->nullable();
            $table->decimal('price_per_unit', 10, 2)->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['plan_id', 'category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_items');
    }
};
