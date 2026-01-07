<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this line

return new class extends Migration
{
    public function up(): void
    {
        // Update the ENUM to include new statuses
        DB::statement("ALTER TABLE `inbounds` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'in_transit', 'receiving', 'completed', 'processing', 'received', 'issue', 'closed') DEFAULT 'draft'");
        
        Schema::table('inbounds', function (Blueprint $table) {
            if (!Schema::hasColumn('inbounds', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('inbounds', 'received_by')) {
                $table->unsignedBigInteger('received_by')->nullable()->after('received_at');
            }
            if (!Schema::hasColumn('inbounds', 'notes_receiving')) {
                $table->text('notes_receiving')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('inbounds', 'has_discrepancies')) {
                $table->boolean('has_discrepancies')->default(false)->after('notes_receiving');
            }
            
            // Re-add indices if they don't exist
            try {
                $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            } catch (\Exception $e) {
                // Ignore if foreign key already exists
            }
        });
    }

    public function down(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['received_at', 'received_by', 'notes_receiving', 'has_discrepancies']);
        });
    }
};
