<?php

namespace App\Enums;

enum PetActionType: string
{
    case Feed = 'feed';
    case Play = 'play';
    case Sleep = 'sleep';
    case Clean = 'clean';
    case Heal = 'heal';
    case Talk = 'talk';

    public function validatePayload(array $payload): bool
    {
        return match ($this) {
            self::Feed => isset($payload['food']) && is_string($payload['food']),
            self::Play => true,
            self::Sleep => true,
            self::Clean => true,
            self::Heal => true,
            self::Talk => isset($payload['message']) && is_string($payload['message']),
        };
    }
}
