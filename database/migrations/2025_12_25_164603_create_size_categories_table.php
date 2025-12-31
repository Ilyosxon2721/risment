<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('code', ['mgt', 'sgt', 'kgt'])->unique();
            $table->integer('sum_min'); // L+W+H min value
            $table->integer('sum_max')->nullable(); // L+W+H max value (null for unlimited)
            $table->decimal('price', 10, 2); // Logistics price: 5000/8000/20000
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_categories');
    }
};
