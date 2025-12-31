<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('reference');
            $table->dateTime('planned_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'processing', 'received', 'issue', 'closed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbounds');
    }
};
