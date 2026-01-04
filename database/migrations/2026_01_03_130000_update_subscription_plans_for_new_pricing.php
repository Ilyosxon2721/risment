<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Add missing fields for new pricing structure
            $table->integer('sku_included')->nullable()->after('fbs_shipments_included');
            $table->string('sla_cutoff_time', 10)->nullable()->after('sla_high'); // Format: "12:00", "14:00", etc.
            
            // Rename storage fields to be more explicit (use box-days and bag-days)
            // Since we already have storage_included_boxes and storage_included_bags,
            // we'll interpret them as days included per month
            // No schema change needed, just documentation change
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['sku_included', 'sla_cutoff_time']);
        });
    }
};
