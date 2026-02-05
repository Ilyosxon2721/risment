<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('billing_invoices')->nullOnDelete();

            $table->integer('amount'); // UZS
            $table->enum('method', ['payme', 'click', 'transfer', 'cash', 'other'])->default('transfer');
            $table->dateTime('paid_at');
            $table->string('external_ref', 100)->nullable(); // Transaction ID from payment gateway
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('invoice_id');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_payments');
    }
};
