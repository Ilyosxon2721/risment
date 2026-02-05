<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('billing_invoices')) {
            return;
        }

        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number', 50)->unique();
            $table->string('period', 7); // YYYY-MM
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])->default('draft');

            // Amounts
            $table->integer('subtotal')->default(0); // Before adjustments
            $table->integer('discount')->default(0);
            $table->integer('total')->default(0);

            // Dates
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'period']);
            $table->index('status');
        });

        // Invoice line items (snapshot from billing_items)
        Schema::create('billing_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('billing_invoices')->onDelete('cascade');
            $table->foreignId('billing_item_id')->nullable()->constrained('billing_items')->nullOnDelete();
            $table->enum('scope', ['inbound', 'pickpack', 'storage', 'shipping', 'returns', 'other']);
            $table->string('title_ru');
            $table->string('title_uz');
            $table->integer('unit_price');
            $table->decimal('qty', 10, 2)->default(1);
            $table->integer('amount');
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_invoice_items');
        Schema::dropIfExists('billing_invoices');
    }
};
