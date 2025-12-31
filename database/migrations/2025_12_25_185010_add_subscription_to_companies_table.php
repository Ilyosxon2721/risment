<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('is_active')->constrained('subscription_plans')->nullOnDelete();
            $table->timestamp('plan_started_at')->nullable()->after('subscription_plan_id');
            $table->enum('plan_status', ['active', 'paused', 'cancelled'])->default('active')->after('plan_started_at');
            $table->tinyInteger('billing_day')->nullable()->after('plan_status')->comment('Day of month for billing (1-28)');
            
            $table->index('subscription_plan_id');
            $table->index('plan_status');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subscription_plan_id', 'plan_started_at', 'plan_status', 'billing_day']);
        });
    }
};
