<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('code', ['onboarding', 'inbound', 'storage', 'packing', 'pickpack', 'logistics', 'fbo_shipping', 'reverse', 'extras'])->unique();
            $table->string('title_ru');
            $table->string('title_uz');
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_categories');
    }
};
