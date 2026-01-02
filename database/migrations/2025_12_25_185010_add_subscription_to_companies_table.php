<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->timestamp('plan_started_at')->nullable();
            $table->enum('plan_status', ['active', 'paused', 'cancelled'])->default('active');
            $table->tinyInteger('billing_day')->nullable()->comment('Day of month for billing (1-28)');
            
            $table->index('subscription_plan_id');
            $table->index('plan_status');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan_id', 'plan_started_at', 'plan_status', 'billing_day']);
        });
    }
};
