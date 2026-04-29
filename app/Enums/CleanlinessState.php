<?php

namespace App\Enums;

enum CleanlinessState: string
{
    case Dirty = 'dirty';
    case Normal = 'normal';
    case Clean = 'clean';

    public static function fromValue(int $value): self
    {
        return match (true) {
            $value <= 30 => self::Dirty,
            $value <= 70 => self::Normal,
            default => self::Clean,
        };
    }

    public function toPromptString(): string
    {
        return match ($this) {
            self::Dirty => 'dirty and needs a bath',
            self::Normal => 'reasonably clean',
            self::Clean => 'freshly groomed and clean',
        };
    }
}
