<?php

namespace App\Enums;

enum HealthState: string
{
    case Critical = 'critical';
    case Sick = 'sick';
    case Normal = 'normal';
    case Healthy = 'healthy';

    public static function fromValue(int $value): self
    {
        return match (true) {
            $value <= 20 => self::Critical,
            $value <= 50 => self::Sick,
            $value <= 80 => self::Normal,
            default => self::Healthy,
        };
    }

    public function toPromptString(): string
    {
        return match ($this) {
            self::Critical => 'in critical condition',
            self::Sick => 'feeling sick and unwell',
            self::Normal => 'in normal health',
            self::Healthy => 'healthy and thriving',
        };
    }
}
