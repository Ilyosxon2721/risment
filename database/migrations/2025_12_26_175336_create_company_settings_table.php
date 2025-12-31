<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_logo')->nullable();
            $table->string('company_name')->default('RISMENT');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address_ru')->nullable();
            $table->text('address_uz')->nullable();
            $table->text('warehouse_address_ru')->nullable();
            $table->text('warehouse_address_uz')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_telegram')->nullable();
            $table->string('stat_orders')->default('10 000+');
            $table->string('stat_sla')->default('99%');
            $table->string('stat_support')->default('24/7');
            $table->string('stat_warehouse_size')->default('5 000+');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
