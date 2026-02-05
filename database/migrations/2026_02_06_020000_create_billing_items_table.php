<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('billing_items')) {
            return;
        }

        Schema::create('billing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('period', 7)->nullable(); // YYYY-MM
            $table->dateTime('billed_at')->nullable();
            $table->enum('scope', ['inbound', 'pickpack', 'storage', 'shipping', 'returns', 'other']);
            $table->string('source_type', 50)->nullable(); // inbound, shipment, return, storage_snapshot
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('addon_code', 50)->nullable();
            // Snapshot fields (frozen at time of billing)
            $table->string('title_ru');
            $table->string('title_uz');
            $table->integer('unit_price'); // in UZS
            $table->decimal('qty', 10, 2)->default(1);
            $table->integer('amount'); // unit_price * qty
            $table->text('comment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'period']);
            $table->index(['company_id', 'scope']);
            $table->index(['source_type', 'source_id']);
            $table->index('addon_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_items');
    }
};
