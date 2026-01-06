<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_product_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            
            // Marketplace info
            $table->enum('marketplace', ['uzum', 'wildberries', 'ozon', 'yandex']);
            $table->string('marketplace_sku')->nullable(); // SKU/артикул на маркетплейсе
            $table->string('marketplace_barcode')->nullable(); // Штрих-код на маркетплейсе
            
            // Sync settings
            $table->boolean('sync_stock')->default(false); // Синхронизировать остатки
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_variant_id', 'marketplace'], 'mpl_variant_mp_idx');
            $table->index(['marketplace', 'marketplace_sku'], 'mpl_mp_sku_idx');
            $table->index(['marketplace', 'marketplace_barcode'], 'mpl_mp_barcode_idx');
            
            // One variant can have only one link per marketplace (but multiple links to different marketplaces)
            $table->unique(['product_variant_id', 'marketplace', 'marketplace_sku'], 'mpl_variant_mp_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_product_links');
    }
};
