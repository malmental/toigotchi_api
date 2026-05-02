<?php

namespace App\Actions\Pets;

use App\Models\Pet;
use App\Models\PetAction;

class HealPetAction implements PetActionContract
{
    public function execute(Pet $pet, array $payload = []): array
    {
        $effects = $this->getEffects();

        $pet->update([
            'health' => min(100, (int) $pet->health + $effects['health']),
        ]);

        PetAction::create([
            'pet_id' => $pet->id,
            'type' => 'heal',
            'payload' => $payload,
            'effects_applied' => $effects,
        ]);

        return $effects;
    }

    public function getEffects(): array
    {
        return [
            'hunger' => 0,
            'mood' => 0,
            'energy' => 0,
            'health' => 20,
            'cleanliness' => 0,
        ];
    }
}
