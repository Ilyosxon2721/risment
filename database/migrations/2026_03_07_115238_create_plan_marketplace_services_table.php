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
        Schema::create('plan_marketplace_services', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('marketplace_service_id');
            $table->primary(['subscription_plan_id', 'marketplace_service_id']);
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('marketplace_service_id')->references('id')->on('marketplace_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_marketplace_services');
    }
};
