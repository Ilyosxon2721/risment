<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('company_name')->nullable();
            $table->json('marketplaces')->nullable(); // ["uzum", "wb"]
            $table->json('schemes')->nullable(); // ["fbo", "fbs"]
            $table->text('comment')->nullable();
            $table->string('source_page')->nullable();
            $table->enum('status', ['new', 'contacted', 'converted', 'rejected'])->default('new');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
