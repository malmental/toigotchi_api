<?php

namespace App\Services;

use App\Actions\Pets\FeedPetAction;
use App\Actions\Pets\PlayWithPetAction;
use App\Actions\Pets\SleepPetAction;
use App\Actions\Pets\CleanPetAction;
use App\Actions\Pets\HealPetAction;
use App\Actions\Pets\TalkPetAction;
use App\Enums\PetActionType;
use App\Models\Pet;

class PetActionManager
{
    public function execute(Pet $pet, PetActionType $type, array $payload = []): array
    {
        $action = match ($type) {
            PetActionType::Feed => new FeedPetAction(),
            PetActionType::Play => new PlayWithPetAction(),
            PetActionType::Sleep => new SleepPetAction(),
            PetActionType::Clean => new CleanPetAction(),
            PetActionType::Heal => new HealPetAction(),
            PetActionType::Talk => new TalkPetAction(),
        };

        return $action->execute($pet, $payload);
    }

    public function canExecute(Pet $pet, PetActionType $type, array $payload = []): bool
    {
        if (!$pet->is_alive) {
            return false;
        }

        return $type->validatePayload($payload);
    }
}
