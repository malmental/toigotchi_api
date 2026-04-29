<?php

namespace App\Actions\Pets;
use App\Models\Pet;
use App\Models\PetAction;

class SleepPetAction implements PetActionContract
{
public function execute(Pet $pet, array $payload = []): array
    {
        $effects = $this->getEffects();

        $pet->update([
            'energy' => min(100, (int) $pet->energy + $effects['energy']),
            'hunger' => min(100, (int) $pet->hunger + $effects['hunger']),
        ]);

        PetAction::create([
            'pet_id' => $pet->id,
            'type' => 'sleep',
            'payload' => $payload,
            'effects_applied' => $effects,
        ]);

        return $effects;
    }

    public function getEffects(): array
    {
        return [
            'hunger' => 10,
            'mood' => 0,
            'energy' => 30,
            'health' => 0,
            'cleanliness' => 0,
        ];
    }
}
