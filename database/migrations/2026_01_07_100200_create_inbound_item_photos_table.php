<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_item_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_item_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->text('description')->nullable();
            $table->enum('issue_type', ['damage', 'missing', 'wrong_item', 'packaging', 'other'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_item_photos');
    }
};
