<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sellermind_sync_status', 20)->default('pending')->after('sellermind_product_id');
            $table->text('sellermind_sync_error')->nullable()->after('sellermind_sync_status');
            $table->timestamp('sellermind_synced_at')->nullable()->after('sellermind_sync_error');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sellermind_sync_status', 'sellermind_sync_error', 'sellermind_synced_at']);
        });
    }
};
