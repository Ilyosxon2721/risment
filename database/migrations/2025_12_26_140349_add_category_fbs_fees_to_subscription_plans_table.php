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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Add category-based FBS overage fees
            $table->decimal('over_fbs_mgt_fee', 10, 2)->default(15000)->after('over_fbs_shipment_fee');
            $table->decimal('over_fbs_sgt_fee', 10, 2)->default(19000)->after('over_fbs_mgt_fee');
            $table->decimal('over_fbs_kgt_fee', 10, 2)->default(32000)->after('over_fbs_sgt_fee');
            
            // Mark old single fee as nullable (deprecated, but keep for backward compatibility)
            $table->decimal('over_fbs_shipment_fee', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['over_fbs_mgt_fee', 'over_fbs_sgt_fee', 'over_fbs_kgt_fee']);
            $table->decimal('over_fbs_shipment_fee', 10, 2)->default(25000)->change();
        });
    }
};
