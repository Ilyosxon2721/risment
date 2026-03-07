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
        Schema::create('plan_service_addons', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('service_addon_id');
            $table->primary(['subscription_plan_id', 'service_addon_id']);
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('service_addon_id')->references('id')->on('service_addons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_service_addons');
    }
};
