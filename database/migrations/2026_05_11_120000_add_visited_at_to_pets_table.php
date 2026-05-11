<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->timestamp('last_visited_at')->nullable()->after('is_alive');
            $table->timestamp('last_decay_at')->nullable()->after('last_visited_at');
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['last_visited_at', 'last_decay_at']);
        });
    }
};