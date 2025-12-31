<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_overage_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->foreignId('overage_rule_id')->constrained('overage_rules')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['plan_id', 'overage_rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_overage_rules');
    }
};
