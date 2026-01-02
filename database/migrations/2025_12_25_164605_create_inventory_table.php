<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('sku_id');
            $table->integer('qty_total')->default(0);
            $table->integer('qty_reserved')->default(0);
            $table->string('location_code')->nullable();
            $table->timestamps();
            
            $table->index('company_id');
            $table->index('sku_id');
            $table->unique(['company_id', 'sku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
