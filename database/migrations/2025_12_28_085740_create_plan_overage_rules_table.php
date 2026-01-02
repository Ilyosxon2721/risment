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
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('overage_rule_id');
            $table->timestamps();
            
            $table->index('plan_id');
            $table->index('overage_rule_id');
            $table->unique(['plan_id', 'overage_rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_overage_rules');
    }
};
