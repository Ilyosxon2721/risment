<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('sku_code')->index();
            $table->string('barcode')->nullable();
            $table->string('title');
            $table->decimal('dims_l', 8, 2)->nullable(); // cm
            $table->decimal('dims_w', 8, 2)->nullable(); // cm
            $table->decimal('dims_h', 8, 2)->nullable(); // cm
            $table->decimal('weight', 8, 3)->nullable(); // kg
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['company_id', 'sku_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
