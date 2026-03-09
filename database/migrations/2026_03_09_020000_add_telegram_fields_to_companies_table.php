<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->after('manager_user_id');
            $table->json('telegram_settings')->nullable()->after('telegram_chat_id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'telegram_settings']);
        });
    }
};
