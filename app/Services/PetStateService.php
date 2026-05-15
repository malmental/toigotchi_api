<?php

namespace App\Domain\Pet\Services;

use App\Enums\CleanlinessState;
use App\Enums\EnergyState;
use App\Enums\HealthState;
use App\Enums\HungerState;
use App\Models\Pet;

class PetStateService
{
    public function getHungerState(Pet $pet): HungerState
    {
        return HungerState::fromValue((int) $pet->hunger);
    }

    public function getEnergyState(Pet $pet): EnergyState
    {
        return EnergyState::fromValue((int) $pet->energy);
    }

    public function getCleanlinessState(Pet $pet): CleanlinessState
    {
        return CleanlinessState::fromValue((int) $pet->cleanliness);
    }

    public function getHealthState(Pet $pet): HealthState
    {
        return HealthState::fromValue((int) $pet->health);
    }

    public function getStateSummary(Pet $pet): array
    {
        return [
            'hunger' => [
                'state' => $this->getHungerState($pet)->value,
                'value' => (int) $pet->hunger,
                'description' => $this->getHungerState($pet)->toPromptString(),
            ],
            'energy' => [
                'state' => $this->getEnergyState($pet)->value,
                'value' => (int) $pet->energy,
                'description' => $this->getEnergyState($pet)->toPromptString(),
            ],
            'cleanliness' => [
                'state' => $this->getCleanlinessState($pet)->value,
                'value' => (int) $pet->cleanliness,
                'description' => $this->getCleanlinessState($pet)->toPromptString(),
            ],
            'health' => [
                'state' => $this->getHealthState($pet)->value,
                'value' => (int) $pet->health,
                'description' => $this->getHealthState($pet)->toPromptString(),
            ],
        ];
    }

    public function toPromptContext(Pet $pet): string
    {
        $summary = $this->getStateSummary($pet);
        $lines = [
            "Hunger: {$summary['hunger']['description']}",
            "Energy: {$summary['energy']['description']}",
            "Cleanliness: {$summary['cleanliness']['description']}",
            "Health: {$summary['health']['description']}",
        ];

        return implode(', ', $lines);
    }
}
