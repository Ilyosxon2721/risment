<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->enum('scheme', ['fbo', 'fbs', 'dbs', 'edbs', 'all'])->default('all');
            $table->enum('marketplace', ['uzum', 'wb', 'ozon', 'yandex', 'all'])->default('all');
            $table->string('title_ru');
            $table->string('title_uz');
            $table->longText('content_ru')->nullable();
            $table->longText('content_uz')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['scheme', 'marketplace', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
