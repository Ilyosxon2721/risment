<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('received_by');
            $table->unsignedBigInteger('confirmed_by_client')->nullable()->after('confirmed_at');
            
            $table->foreign('confirmed_by_client')->references('id')->on('users')->onDelete('set null');
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
