<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellermind_account_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('sellermind_user_id')->nullable();
            $table->unsignedBigInteger('sellermind_company_id')->nullable();
            $table->string('link_token', 64)->unique();
            $table->boolean('sync_products')->default(true);
            $table->boolean('sync_orders')->default(true);
            $table->boolean('sync_stock')->default(true);
            $table->enum('status', ['pending', 'active', 'disabled'])->default('pending');
            $table->timestamp('linked_at')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('link_token');
            $table->index('status');
        });

        // Add sellermind_product_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('sellermind_product_id')->nullable()->after('is_active');
            $table->index('sellermind_product_id');
        });

        // Add sellermind_order_id to shipments_fbo table
        Schema::table('shipments_fbo', function (Blueprint $table) {
            $table->unsignedBigInteger('sellermind_order_id')->nullable()->after('status');
            $table->index('sellermind_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('shipments_fbo', function (Blueprint $table) {
            $table->dropIndex(['sellermind_order_id']);
            $table->dropColumn('sellermind_order_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['sellermind_product_id']);
            $table->dropColumn('sellermind_product_id');
        });

        Schema::dropIfExists('sellermind_account_links');
    }
};
