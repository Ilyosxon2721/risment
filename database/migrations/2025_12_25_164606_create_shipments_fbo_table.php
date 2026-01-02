<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments_fbo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->enum('marketplace', ['uzum', 'wb', 'ozon', 'yandex']);
            $table->string('warehouse_name');
            $table->dateTime('planned_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'picking', 'packed', 'shipped', 'closed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments_fbo');
    }
};
