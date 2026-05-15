<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_decay_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->integer('hours_elapsed');
            $table->json('changes');
            $table->timestamp('created_at');

            $table->index(['pet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_decay_logs');
    }
};