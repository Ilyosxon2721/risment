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
        Schema::create('company_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            // percent = % скидка, fixed = фиксированная сумма в UZS
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            // На что применяется скидка
            $table->enum('target', ['subscription', 'overage', 'all'])->default('all');
            $table->decimal('value', 10, 2); // 0-100 для percent, сумма для fixed
            $table->string('reason')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_discounts');
    }
};
