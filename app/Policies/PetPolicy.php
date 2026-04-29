<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    public function view(User $user, Pet $pet): bool
    {
        return $user->is($pet->user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->is($pet->user);
    }

    public function delete(User $user, Pet $pet): bool
    {
        return $user->is($pet->user);
    }
}
