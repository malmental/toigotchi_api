<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetQuotaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'used_count' => 0,
            'window_start' => now(),
        ];
    }

    public function exhausted(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_count' => 3,
        ]);
    }

    public function windowExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'window_start' => now()->subMinutes(65),
        ]);
    }
}