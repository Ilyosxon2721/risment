<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // PICKPACK_UNIT, DELIVERY_MGT, etc.
            $table->decimal('value', 15, 2); // UZS
            $table->string('unit_ru', 50); // "сум за отправку", "сум/месяц"
            $table->string('unit_uz', 50);
            $table->text('description_ru')->nullable();
            $table->text('description_uz')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rates');
    }
};
