<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug', 100); // pricing, calculator, etc
            $table->string('block_key', 100); // hero_title, plan_lite_tagline, etc
            $table->string('title_ru')->nullable();
            $table->string('title_uz')->nullable();
            $table->text('body_ru')->nullable();
            $table->text('body_uz')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['page_slug', 'block_key']);
            $table->index('page_slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
