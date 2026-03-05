<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL: update ENUM, for SQLite: status is already string, no action needed
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `inbounds` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'in_transit', 'receiving', 'completed', 'processing', 'received', 'issue', 'closed') DEFAULT 'draft'");
        }
        // SQLite stores status as string, which accepts any value - no modification needed

        Schema::table('inbounds', function (Blueprint $table) {
            if (!Schema::hasColumn('inbounds', 'received_at')) {
                $table->timestamp('received_at')->nullable();
            }
            if (!Schema::hasColumn('inbounds', 'received_by')) {
                $table->unsignedBigInteger('received_by')->nullable();
            }
            if (!Schema::hasColumn('inbounds', 'notes_receiving')) {
                $table->text('notes_receiving')->nullable();
            }
            if (!Schema::hasColumn('inbounds', 'has_discrepancies')) {
                $table->boolean('has_discrepancies')->default(false);
            }
        });

        // Add foreign key separately for MySQL only (SQLite doesn't support adding FK to existing table)
        if (DB::getDriverName() === 'mysql') {
            Schema::table('inbounds', function (Blueprint $table) {
                try {
                    $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Ignore if foreign key already exists
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                $table->dropForeign(['received_by']);
            }
            $columns = [];
            foreach (['received_at', 'received_by', 'notes_receiving', 'has_discrepancies'] as $col) {
                if (Schema::hasColumn('inbounds', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
