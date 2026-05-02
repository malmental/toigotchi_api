<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'user1@telsur.cl')->first();

        Pet::factory()->create([
            'user_id' => $user1->id,
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
            'user_id' => $user1->id,
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
            'user_id' => $user1->id,
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
            'user_id' => $user1->id,
            'name' => 'Ghost',
            'species' => 'blobcat',
            'health' => 0,
            'energy' => 0,
            'hunger' => 100,
            'cleanliness' => 10,
            'mood' => 'angry',
            'is_alive' => false,
        ]);

        $user2 = User::where('email', 'user2@telsur.cl')->first();

        Pet::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Luna',
            'species' => 'foxkid',
            'health' => 90,
            'energy' => 70,
            'hunger' => 20,
            'cleanliness' => 85,
            'mood' => 'happy',
            'is_alive' => true,
        ]);

        Pet::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Zephyr',
            'species' => 'draggle',
            'health' => 50,
            'energy' => 40,
            'hunger' => 50,
            'cleanliness' => 60,
            'mood' => 'neutral',
            'is_alive' => true,
        ]);

        Pet::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Frost',
            'species' => 'foxkid',
            'health' => 75,
            'energy' => 65,
            'hunger' => 35,
            'cleanliness' => 80,
            'mood' => 'happy',
            'is_alive' => true,
        ]);

        Pet::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Nova',
            'species' => 'blobcat',
            'health' => 40,
            'energy' => 55,
            'hunger' => 45,
            'cleanliness' => 70,
            'mood' => 'neutral',
            'is_alive' => true,
        ]);
    }
}
