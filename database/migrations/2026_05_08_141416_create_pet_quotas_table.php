<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->unique()->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('used_count')->default(0);
            $table->timestamp('window_start')->nullable();
            $table->timestamps();

            $table->index(['pet_id', 'window_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_quotas');
    }
};