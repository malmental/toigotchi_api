<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $salem = User::where('email', 'salem@telsur.cl')->first();

        Pet::factory()->create([
            'user_id' => $salem->id,
            'name' => 'Mochi',
            'species' => 'blobcat',
            'health' => 100,
            'energy' => 85,
            'hunger' => 15,
            'cleanliness' => 90,
            'mood' => 'happy',
            'is_alive' => true,
        ]);

        Pet::factory()->create([
            'user_id' => $salem->id,
            'name' => 'Ember',
            'species' => 'foxkid',
            'health' => 60,
            'energy' => 30,
            'hunger' => 70,
            'cleanliness' => 55,
            'mood' => 'tired',
            'is_alive' => true,
        ]);

        Pet::factory()->create([
            'user_id' => $salem->id,
            'name' => 'Scales',
            'species' => 'draggle',
            'health' => 20,
            'energy' => 10,
            'hunger' => 95,
            'cleanliness' => 40,
            'mood' => 'angry',
            'is_alive' => true,
        ]);

        Pet::factory()->create([
            'user_id' => $salem->id,
            'name' => 'Ghost',
            'species' => 'blobcat',
            'health' => 0,
            'energy' => 0,
            'hunger' => 100,
            'cleanliness' => 10,
            'mood' => 'angry',
            'is_alive' => false,
        ]);
    }
}
