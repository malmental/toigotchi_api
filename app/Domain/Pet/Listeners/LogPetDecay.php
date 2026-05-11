<?php

namespace App\Domain\Pet\Listeners;

use App\Domain\Pet\Events\PetDecayedWhileAway;
use App\Models\PetDecayLog;

class LogPetDecay
{
    public function handle(PetDecayedWhileAway $event): void
    {
        PetDecayLog::create([
            'pet_id' => $event->pet->id,
            'hours_elapsed' => $event->hoursElapsed,
            'changes' => $event->changes,
            'created_at' => now(),
        ]);
    }
}