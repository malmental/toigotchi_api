<?php

namespace App\Actions\Pets;
use App\Models\Pet;
use App\Models\PetAction;

class PlayWithPetAction implements PetActionContract
{
public function execute(Pet $pet, array $payload = []): array
    {
        $effects = $this->getEffects();

        $pet->update([
            'energy' => max(0, (int) $pet->energy + $effects['energy']),
            'mood' => min(100, (int) $pet->mood + $effects['mood']),
        ]);

        PetAction::create([
            'pet_id' => $pet->id,
            'type' => 'play',
            'payload' => $payload,
            'effects_applied' => $effects,
        ]);

        return $effects;
    }

    public function getEffects(): array
    {
        return [
            'hunger' => 0,
            'mood' => 10,
            'energy' => -15,
            'health' => 0,
            'cleanliness' => 0,
        ];
    }
}