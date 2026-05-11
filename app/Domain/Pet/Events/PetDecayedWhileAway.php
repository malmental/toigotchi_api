<?php

namespace App\Domain\Pet\Events;

use App\Models\Pet;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PetDecayedWhileAway
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Pet $pet,
        public readonly int $hoursElapsed,
        public readonly array $changes,
    ) {}
}