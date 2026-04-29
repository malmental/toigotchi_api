<?php

namespace App\Domain\Pet\ValueObjects;

class PetStats
{
    public function __construct(
        public readonly int $health,
        public readonly int $energy,
        public readonly int $hunger,
        public readonly int $cleanliness,
    ) {}

    public static function fromPet($pet): self
    {
        return new self(
            health: (int) $pet->health,
            energy: (int) $pet->energy,
            hunger: (int) $pet->hunger,
            cleanliness: (int) $pet->cleanliness,
        );
    }

    public function clamp(): self
    {
        return new self(
            health: max(0, min(100, $this->health)),
            energy: max(0, min(100, $this->energy)),
            hunger: max(0, min(100, $this->hunger)),
            cleanliness: max(0, min(100, $this->cleanliness)),
        );
    }

    public function isDead(): bool
    {
        return $this->hunger >= 100 || $this->health <= 0;
    }

    public function toArray(): array
    {
        return [
            'health' => $this->health,
            'energy' => $this->energy,
            'hunger' => $this->hunger,
            'cleanliness' => $this->cleanliness,
        ];
    }
}
