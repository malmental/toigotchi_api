<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'species' => fake()->randomElement(['blobcat', 'foxkid', 'draggle']),
            'health' => 100,
            'energy' => 100,
            'hunger' => 0,
            'cleanliness' => 100,
            'mood' => 'happy',
            'is_alive' => true,
        ];
    }
}
