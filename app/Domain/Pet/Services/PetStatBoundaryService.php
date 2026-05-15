<?php

namespace App\Domain\Pet\Services;

use App\Domain\Pet\ValueObjects\PetStats;
use App\Models\Pet;

class PetStatBoundaryService
{
    public function clamp(Pet $pet): Pet
    {
        $stats = PetStats::fromPet($pet)->clamp();

        $pet->update([
            'health' => $stats->health,
            'energy' => $stats->energy,
            'hunger' => $stats->hunger,
            'cleanliness' => $stats->cleanliness,
        ]);

        return $pet->fresh();
    }

    public function isDead(Pet $pet): bool
    {
        return PetStats::fromPet($pet)->isDead();
    }

    public function killPet(Pet $pet): Pet
    {
        $pet->update(['is_alive' => false]);

        return $pet->fresh();
    }
}
