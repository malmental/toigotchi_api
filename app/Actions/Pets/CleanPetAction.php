<?php

namespace App\Actions\Pets;

use App\Models\Pet;
use App\Models\PetAction;

class CleanPetAction implements PetActionContract
{
    public function execute(Pet $pet, array $payload = []): array
    {
        $effects = $this->getEffects();

        $pet->update([
            'cleanliness' => min(100, (int) $pet->cleanliness + $effects['cleanliness']),
            'mood' => min(100, (int) $pet->mood + $effects['mood']),
        ]);

        PetAction::create([
            'pet_id' => $pet->id,
            'type' => 'clean',
            'payload' => $payload,
            'effects_applied' => $effects,
        ]);

        return $effects;
    }

    public function getEffects(): array
    {
        return [
            'hunger' => 0,
            'mood' => 5,
            'energy' => 0,
            'health' => 0,
            'cleanliness' => 25,
        ];
    }
}
