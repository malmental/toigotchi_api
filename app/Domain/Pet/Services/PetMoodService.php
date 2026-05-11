<?php

namespace App\Domain\Pet\Services;

use App\Domain\Pet\ValueObjects\PetStats;
use App\Models\Pet;

class PetMoodService
{
    public function calculateMood(Pet $pet): string
    {
        $stats = PetStats::fromPet($pet);
        if ($stats->hunger >= 80 || $stats->health <= 20) {
            return 'angry';
        }
        if ($stats->energy <= 20) {
            return 'tired';
        }
        if ($stats->cleanliness <= 30) {
            return 'dirty';
        }
        if ($stats->hunger <= 20 && $stats->health >= 80 && $stats->energy >= 50) {
            return 'happy';
        }

        return 'neutral';
    }

    public function updateMood(Pet $pet): Pet
    {
        $mood = $this->calculateMood($pet);
        $pet->update(['mood' => $mood]);

        return $pet->fresh();
    }
}
