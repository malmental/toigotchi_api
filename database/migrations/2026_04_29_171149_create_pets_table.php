<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('species');
            $table->integer('health')->default(100);
            $table->integer('energy')->default(100);
            $table->integer('hunger')->default(0);
            $table->integer('cleanliness')->default(100);
            $table->string('mood')->default('happy');
            $table->boolean('is_alive')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
