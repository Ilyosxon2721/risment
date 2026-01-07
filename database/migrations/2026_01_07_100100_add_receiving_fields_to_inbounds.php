<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this line

return new class extends Migration
{
    public function up(): void
    {
        // Update the ENUM to include new statuses: in_transit, receiving, completed
        DB::statement("ALTER TABLE `inbounds` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'in_transit', 'receiving', 'completed', 'processing', 'received', 'issue', 'closed') DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['received_at', 'received_by', 'notes_receiving', 'has_discrepancies']);
        });
    }
};
