<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Basic product info
            $table->string('title'); // Название товара
            $table->string('article')->index(); // Артикул товара
            $table->text('description')->nullable(); // Описание (опционально)
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'is_active']);
            $table->unique(['company_id', 'article']); // Артикул уникален в рамках компании
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
