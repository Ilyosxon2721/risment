<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Variant info
            $table->string('variant_name')->nullable(); // Например: "Красный, XL"
            $table->string('sku_code')->unique(); // Уникальный код варианта
            $table->string('barcode')->nullable(); // Штрих-код варианта
            
            // Dimensions & weight
            $table->decimal('dims_l', 8, 2)->nullable(); // Длина в см
            $table->decimal('dims_w', 8, 2)->nullable(); // Ширина в см
            $table->decimal('dims_h', 8, 2)->nullable(); // Высота в см
            $table->decimal('weight', 8, 3)->nullable(); // Вес в кг
            
            // Pricing (optional)
            $table->decimal('price', 12, 2)->nullable(); // Цена
            $table->decimal('cost_price', 12, 2)->nullable(); // Себестоимость
            $table->decimal('expenses', 12, 2)->nullable(); // Расходы
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'is_active']);
            $table->index('sku_code');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
