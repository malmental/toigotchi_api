<?php

namespace App\Enums;

enum HungerState: string
{
    case Full = 'full';
    case Satisfied = 'satisfied';
    case Hungry = 'hungry';
    case Starving = 'starving';

    public static function fromValue(int $value): self
    {
        return match (true) {
            $value <= 10 => self::Full,
            $value <= 40 => self::Satisfied,
            $value <= 70 => self::Hungry,
            default => self::Starving,
        };
    }

    public function toPromptString(): string
    {
        return match ($this) {
            self::Full => 'completely full and content',
            self::Satisfied => 'satisfied with a full belly',
            self::Hungry => 'getting hungry',
            self::Starving => 'starving and desperate for food',
        };
    }
}
