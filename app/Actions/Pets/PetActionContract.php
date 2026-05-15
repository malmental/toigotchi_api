<?php

namespace App\Actions\Pets;

use App\Models\Pet;

interface PetActionContract
{
    public function execute(Pet $pet, array $payload = []): array;

    public function getEffects(): array;
}
