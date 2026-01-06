<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Add product_variant_id for new system
            $table->foreignId('product_variant_id')->nullable()->after('sku_id')->constrained()->onDelete('cascade');
            
            // Make sku_id nullable for backward compatibility
            $table->foreignId('sku_id')->nullable()->change();
            
            // Add index
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
