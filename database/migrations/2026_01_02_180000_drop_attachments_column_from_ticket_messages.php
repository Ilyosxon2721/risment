<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This removes the deprecated 'attachments' JSON column from ticket_messages.
     * Attachments are now stored in the separate ticket_attachments table.
     */
    public function up(): void
    {
        if (Schema::hasColumn('ticket_messages', 'attachments')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->dropColumn('attachments');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('ticket_messages', 'attachments')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->json('attachments')->nullable()->after('message');
            });
        }
    }
};
