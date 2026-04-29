<?php

namespace App\Domain\Pet\Services;

class SpeciesModifierService
{
    private const MODIFIERS = [
        'blobcat' => [
            'hunger_decay_rate' => 5,
            'cleanliness_decay_rate' => 2,
            'happiness_boost_rate' => 1.0,
            'food_efficiency' => 1.0,
        ],
        'foxkid' => [
            'hunger_decay_rate' => 4,
            'cleanliness_decay_rate' => 3,
            'happiness_boost_rate' => 1.5,
            'food_efficiency' => 1.1,
        ],
        'draggle' => [
            'hunger_decay_rate' => 3,
            'cleanliness_decay_rate' => 1,
            'happiness_boost_rate' => 0.8,
            'food_efficiency' => 0.8,
        ],
    ];

    public function getHungerDecayRate(string $species): int
    {
        return self::MODIFIERS[$species]['hunger_decay_rate'] ?? 5;
    }

    public function getCleanlinessDecayRate(string $species): int
    {
        return self::MODIFIERS[$species]['cleanliness_decay_rate'] ?? 2;
    }

    public function getHappinessBoostRate(string $species): float
    {
        return self::MODIFIERS[$species]['happiness_boost_rate'] ?? 1.0;
    }

    public function getFoodEfficiency(string $species): float
    {
        return self::MODIFIERS[$species]['food_efficiency'] ?? 1.0;
    }

    public function getModifiers(string $species): array
    {
        return self::MODIFIERS[$species] ?? self::MODIFIERS['blobcat'];
    }

    public function getDecayValues(string $species): array
    {
        return [
            'hunger' => $this->getHungerDecayRate($species),
            'cleanliness' => $this->getCleanlinessDecayRate($species),
        ];
    }
}
