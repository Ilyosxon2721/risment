<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            // Ensure fields from previous migration also exist (for server sync)
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

            // Confirmation fields
            if (!Schema::hasColumn('inbounds', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('inbounds', 'confirmed_by_client')) {
                $table->unsignedBigInteger('confirmed_by_client')->nullable()->after('confirmed_at');
            }
            
            // Re-add indices safely
            try {
                if (!Schema::hasColumn('inbounds', 'received_by')) {
                    $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
                }
            } catch (\Exception $e) {}

            try {
                $table->foreign('confirmed_by_client')->references('id')->on('users')->onDelete('set null');
            } catch (\Exception $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by_client']);
            $table->dropColumn(['confirmed_at', 'confirmed_by_client']);
        });
    }
};
