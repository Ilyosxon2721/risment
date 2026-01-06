<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            
            // Attribute info
            $table->string('attribute_name'); // Название характеристики (цвет, размер, модель)
            $table->string('attribute_value'); // Значение характеристики (красный, XL, ABC-123)
            
            $table->timestamps();
            
            // Indexes
            $table->index('product_variant_id');
            $table->index(['product_variant_id', 'attribute_name'], 'pva_variant_attr_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
    }
};
