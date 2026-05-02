<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetMemory;
use Illuminate\Database\Seeder;

class PetMemorySeeder extends Seeder
{
    public function run(): void
    {
        $mochi = Pet::where('name', 'Mochi')->first();

        if (! $mochi) {
            return;
        }

        PetMemory::create([
            'pet_id' => $mochi->id,
            'type' => 'conversation',
            'content' => 'Hello Mochi! How are you today?',
            'ai_response' => 'I am happy to see you! I was just playing with my favorite toy.',
            'importance' => 7,
        ]);

        PetMemory::create([
            'pet_id' => $mochi->id,
            'type' => 'conversation',
            'content' => 'Do you like apples?',
            'ai_response' => 'Yes! Apples are my favorite snack. They make me feel full and happy!',
            'importance' => 5,
        ]);

        PetMemory::create([
            'pet_id' => $mochi->id,
            'type' => 'action',
            'content' => 'User fed Mochi with fish',
            'ai_response' => null,
            'importance' => 8,
        ]);

        PetMemory::create([
            'pet_id' => $mochi->id,
            'type' => 'event',
            'content' => 'Mochi became angry because hunger was too high',
            'ai_response' => null,
            'importance' => 9,
        ]);
    }
}
