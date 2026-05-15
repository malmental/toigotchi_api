<?php

namespace App\Enums;

enum EnergyState: string
{
    case Exhausted = 'exhausted';
    case Tired = 'tired';
    case Normal = 'normal';
    case Energetic = 'energetic';

    public static function fromValue(int $value): self
    {
        return match (true) {
            $value <= 20 => self::Exhausted,
            $value <= 40 => self::Tired,
            $value <= 70 => self::Normal,
            default => self::Energetic,
        };
    }

    public function toPromptString(): string
    {
        return match ($this) {
            self::Exhausted => 'completely exhausted and barely able to move',
            self::Tired => 'tired and sluggish',
            self::Normal => 'in normal condition',
            self::Energetic => 'full of energy and eager to play',
        };
    }
}
