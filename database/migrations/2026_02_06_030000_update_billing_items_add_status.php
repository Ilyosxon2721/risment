<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            // Add status for ledger workflow
            $table->enum('status', ['accrued', 'void', 'invoiced'])->default('accrued')->after('amount');

            // Link to invoice when invoiced
            $table->foreignId('invoice_id')->nullable()->after('status')->constrained('billing_invoices')->nullOnDelete();

            // When the operation actually occurred (vs billed_at which is when we recorded it)
            $table->dateTime('occurred_at')->nullable()->after('billed_at');

            // Prevent duplicate billing (composite key: source_type + source_id + addon_code)
            $table->string('idempotency_key', 100)->nullable()->unique()->after('invoice_id');

            // Extra data (category, rate used, formula, etc.)
            $table->json('meta')->nullable()->after('comment');

            $table->index('status');
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['invoice_id']);
            $table->dropForeign(['invoice_id']);
            $table->dropColumn(['status', 'invoice_id', 'occurred_at', 'idempotency_key', 'meta']);
        });
    }
};
