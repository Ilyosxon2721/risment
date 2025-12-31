<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // lite, start, pro, business, enterprise
            $table->string('name_ru');
            $table->string('name_uz');
            $table->text('description_ru')->nullable();
            $table->text('description_uz')->nullable();
            
            // Pricing
            $table->decimal('price_month', 15, 2); // UZS per month
            $table->boolean('is_custom')->default(false); // for ENTERPRISE
            $table->decimal('min_price_month', 15, 2)->nullable(); // for ENTERPRISE minimum
            
            // Included limits
            $table->integer('fbs_shipments_included')->nullable(); // orders/shipments per month
            $table->integer('storage_included_boxes')->nullable(); // 60x40x40 boxes
            $table->integer('storage_included_bags')->nullable(); // bags
            $table->integer('inbound_included_boxes')->nullable(); // inbound boxes per month
            
            // Service features
            $table->boolean('shipping_included')->default(false); // FBS shipping included
            $table->string('schedule_ru')->nullable(); // "3 раза в неделю: Пн/Ср/Пт, cut-off 12:00"
            $table->string('schedule_uz')->nullable();
            $table->boolean('priority_processing')->default(false);
            $table->boolean('sla_high')->default(false);
            $table->boolean('personal_manager')->default(false);
            
            // Overage fees
            $table->decimal('over_fbs_shipment_fee', 10, 2)->default(25000); // per shipment
            $table->decimal('over_storage_box_fee', 10, 2)->default(18000); // per box per month
            $table->decimal('over_storage_bag_fee', 10, 2)->default(12000); // per bag per month
            $table->decimal('over_inbound_box_fee', 10, 2)->default(15000); // per box
            
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
