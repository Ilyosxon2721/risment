<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            
            // Payment details
            $table->string('gateway', 20); // 'click' or 'payme'
            $table->string('transaction_id', 100)->unique()->nullable();
            $table->string('merchant_trans_id', 100)->nullable();
            
            // Amount
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('UZS');
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, processing, completed, failed, cancelled
            
            // Gateway specific data
            $table->json('gateway_data')->nullable();
            
            // Callbacks
            $table->string('prepare_id', 100)->nullable();
            $table->timestamp('callback_time')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_id');
            $table->index('status');
            $table->index('gateway');
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
