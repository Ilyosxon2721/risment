<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_task_id')->nullable()->after('source_id');
            $table->index('manager_task_id');
        });
    }

    public function down(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            $table->dropIndex(['manager_task_id']);
            $table->dropColumn('manager_task_id');
        });
    }
};
