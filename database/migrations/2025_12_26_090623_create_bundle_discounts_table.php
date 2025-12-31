<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bundle_discounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['management'])->comment('Discount type: management, launch, etc.');
            $table->unsignedInteger('marketplaces_count')->comment('Number of marketplaces: 2, 3, 4');
            $table->decimal('discount_percent', 5, 2)->comment('Discount percentage: 7.00, 12.00, 18.00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure unique combination of type + marketplaces_count
            $table->unique(['type', 'marketplaces_count']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_discounts');
    }
};
