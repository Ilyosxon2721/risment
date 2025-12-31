<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('month', 7); // YYYY-MM format
            
            // Usage counters
            $table->integer('fbs_shipments_count')->default(0);
            $table->integer('inbound_boxes_count')->default(0);
            $table->integer('storage_boxes_peak')->default(0); // peak boxes during month
            $table->integer('storage_bags_peak')->default(0); // peak bags during month
            
            $table->timestamps();
            
            $table->unique(['company_id', 'month']);
            $table->index('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_usages');
    }
};
