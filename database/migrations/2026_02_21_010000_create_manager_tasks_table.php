<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('confirmed_by')->nullable();

            $table->enum('task_type', ['inbound', 'pickpack', 'delivery', 'storage', 'return']);
            $table->enum('source', ['manual', 'sellermind'])->default('manual');
            $table->enum('status', ['pending_confirmation', 'confirmed', 'rejected'])->default('pending_confirmation');

            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();

            $table->json('details')->nullable();
            $table->text('comment')->nullable();
            $table->dateTime('task_date');
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();

            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['company_id', 'status']);
            $table->index(['source', 'status']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_tasks');
    }
};
