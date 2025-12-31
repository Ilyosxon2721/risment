<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_services', function (Blueprint $table) {
            $table->id();
            
            // Service categorization
            $table->enum('service_group', ['launch', 'management', 'ads_addon', 'infographics']);
            $table->enum('marketplace', ['uzum', 'wildberries', 'ozon', 'yandex', 'all'])->nullable();
            $table->string('code', 50)->unique();
            
            // Localized content
            $table->string('name_ru');
            $table->string('name_uz');
            $table->text('description_ru')->nullable();
            $table->text('description_uz')->nullable();
            $table->string('unit_ru', 50); // "разово", "в месяц", "за товар"
            $table->string('unit_uz', 50);
            
            // Pricing
            $table->decimal('price', 12, 2);
            $table->integer('sku_limit')->nullable(); // For launch/management packages
            
            // Ordering & status
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('service_group');
            $table->index('marketplace');
            $table->index('is_active');
            $table->index(['service_group', 'marketplace', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_services');
    }
};
