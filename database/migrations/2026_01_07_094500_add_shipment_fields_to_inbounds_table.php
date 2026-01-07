<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->text('shipping_address')->nullable()->after('notes');
            $table->string('executor_name')->nullable()->after('shipping_address');
            $table->string('executor_phone')->nullable()->after('executor_name');
        });
    }

    public function down(): void
    {
        Schema::table('inbounds', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'executor_name', 'executor_phone']);
        });
    }
};
