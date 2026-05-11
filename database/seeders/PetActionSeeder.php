<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetAction;
use Illuminate\Database\Seeder;

class PetActionSeeder extends Seeder
{
    public function run(): void
    {
        $mochi = Pet::where('name', 'Mochi')->first();

        if (! $mochi) {
            return;
        }

        PetAction::create([
            'pet_id' => $mochi->id,
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
            'effects_applied' => ['hunger' => -20, 'mood' => 5],
        ]);

        PetAction::create([
            'pet_id' => $mochi->id,
            'type' => 'play',
            'payload' => [],
            'effects_applied' => ['energy' => -15, 'mood' => 10],
        ]);

        PetAction::create([
            'pet_id' => $mochi->id,
            'type' => 'sleep',
            'payload' => [],
            'effects_applied' => ['energy' => 30, 'hunger' => 10],
        ]);
    }
}
