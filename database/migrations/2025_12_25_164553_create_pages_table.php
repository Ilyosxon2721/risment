<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ru');
            $table->string('title_uz');
            $table->longText('content_ru')->nullable();
            $table->longText('content_uz')->nullable();
            $table->string('meta_title_ru')->nullable();
            $table->string('meta_title_uz')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_uz')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
