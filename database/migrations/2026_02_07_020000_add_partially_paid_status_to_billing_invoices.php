<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter enum to include 'partially_paid' status (MySQL only)
        // SQLite stores status as string which accepts any value
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE billing_invoices MODIFY COLUMN status ENUM('draft', 'issued', 'partially_paid', 'paid', 'overdue', 'cancelled') DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE billing_invoices MODIFY COLUMN status ENUM('draft', 'issued', 'paid', 'overdue', 'cancelled') DEFAULT 'draft'");
        }
    }
};
