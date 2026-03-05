<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update ENUM to include all task types (MySQL only)
        // SQLite stores task_type as string which accepts any value
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `manager_tasks` MODIFY COLUMN `task_type` ENUM('inbound', 'pickpack', 'delivery', 'shipping', 'storage', 'return', 'packaging', 'labeling', 'photo', 'inventory_check', 'other') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `manager_tasks` MODIFY COLUMN `task_type` ENUM('inbound', 'pickpack', 'delivery', 'storage', 'return', 'other') NOT NULL");
        }
    }
};
