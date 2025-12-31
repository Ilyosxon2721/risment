<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->integer('included_shipments')->default(0);
            $table->integer('included_boxes')->default(0);
            $table->integer('included_bags')->default(0);
            $table->integer('included_inbound_boxes')->default(0);
            $table->timestamps();
            
            $table->index('plan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_limits');
    }
};
