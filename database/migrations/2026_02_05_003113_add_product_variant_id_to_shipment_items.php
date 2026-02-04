<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('sku_id')
                ->constrained('product_variants')->nullOnDelete();
            $table->string('item_name')->nullable()->after('product_variant_id');
            $table->decimal('price', 12, 2)->nullable()->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_variant_id', 'item_name', 'price']);
        });
    }
};
