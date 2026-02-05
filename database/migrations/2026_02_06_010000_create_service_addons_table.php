<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_addons')) {
            return;
        }

        Schema::create('service_addons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('scope', ['inbound', 'pickpack', 'storage', 'shipping', 'returns', 'other']);
            $table->string('title_ru');
            $table->string('title_uz');
            $table->string('unit_ru', 100);
            $table->string('unit_uz', 100);
            $table->enum('pricing_type', ['fixed', 'by_category', 'percent', 'manual']);
            $table->decimal('value', 12, 2)->nullable();
            $table->json('meta')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_uz')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->index(['scope', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_addons');
    }
};
