<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('billing_invoices', 'period')) {
                $table->string('period', 7)->nullable()->after('invoice_number');
            }
            if (!Schema::hasColumn('billing_invoices', 'discount')) {
                $table->integer('discount')->default(0)->after('tax');
            }
            if (!Schema::hasColumn('billing_invoices', 'issued_at')) {
                $table->dateTime('issued_at')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('billing_invoices', 'due_at')) {
                $table->dateTime('due_at')->nullable()->after('issued_at');
            }
            if (!Schema::hasColumn('billing_invoices', 'paid_at')) {
                $table->dateTime('paid_at')->nullable()->after('due_at');
            }
            if (!Schema::hasColumn('billing_invoices', 'cancelled_at')) {
                $table->dateTime('cancelled_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('billing_invoices', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes');
            }
        });

        // Backfill period from period_start for existing rows
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE billing_invoices SET period = DATE_FORMAT(period_start, '%Y-%m') WHERE period IS NULL AND period_start IS NOT NULL"
        );
    }

    public function down(): void
    {
        Schema::table('billing_invoices', function (Blueprint $table) {
            $columns = ['period', 'discount', 'issued_at', 'due_at', 'paid_at', 'cancelled_at', 'created_by'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('billing_invoices', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
