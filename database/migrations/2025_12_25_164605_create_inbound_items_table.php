<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->integer('qty_planned')->default(0);
            $table->integer('qty_received')->nullable();
            $table->integer('qty_diff')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('inbound_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_items');
    }
};
