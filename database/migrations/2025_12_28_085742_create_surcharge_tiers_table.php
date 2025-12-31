<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surcharge_tiers', function (Blueprint $table) {
            $table->id();
            $table->integer('min_shipments');
            $table->integer('max_shipments')->nullable(); // NULL = unlimited
            $table->decimal('surcharge_percent', 5, 2); // 0, 10, 20
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surcharge_tiers');
    }
};
