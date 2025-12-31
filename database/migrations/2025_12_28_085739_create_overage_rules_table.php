<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overage_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('scope', ['plan'])->default('plan');
            $table->enum('type', ['shipments', 'storage_boxes', 'storage_bags', 'inbound_boxes']);
            $table->enum('pricing_mode', ['per_unit_base', 'fixed_by_category', 'fixed']);
            $table->decimal('fee_mgt', 10, 2)->nullable();
            $table->decimal('fee_sgt', 10, 2)->nullable();
            $table->decimal('fee_kgt', 10, 2)->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overage_rules');
    }
};
