<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('marketplace', ['wildberries', 'ozon', 'uzum', 'yandex_market']);
            $table->string('name', 100);

            // Wildberries
            $table->text('wb_api_token')->nullable();
            $table->string('wb_supplier_id', 50)->nullable();

            // Ozon
            $table->string('ozon_client_id', 50)->nullable();
            $table->text('ozon_api_key')->nullable();

            // Uzum
            $table->text('uzum_api_token')->nullable();
            $table->string('uzum_seller_id', 50)->nullable();

            // Yandex Market
            $table->text('yandex_oauth_token')->nullable();
            $table->string('yandex_campaign_id', 50)->nullable();
            $table->string('yandex_business_id', 50)->nullable();

            $table->boolean('is_active')->default(true);

            // SellerMind link
            $table->unsignedBigInteger('sellermind_account_id')->nullable();
            $table->timestamp('synced_to_sellermind_at')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'marketplace', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_credentials');
    }
};
