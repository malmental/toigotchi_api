<?php

namespace App\Console\Commands;

use App\Domain\Pet\Events\PetDecayedWhileAway;
use App\Domain\Pet\Services\PetMoodService;
use App\Domain\Pet\Services\SpeciesModifierService;
use App\Models\Pet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class HourlyDecayCommand extends Command
{
    protected $signature = 'pets:hourly-decay';

    protected $description = 'Apply hourly stat decay to all live pets (max 24h cap)';

    private const MAX_HOURS_CAP = 24;

    public function __construct(
        private SpeciesModifierService $speciesService,
        private PetMoodService $moodService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = 0;

        Pet::where('is_alive', true)->chunkById(100, function ($pets) use (&$count) {
            foreach ($pets as $pet) {
                $this->applyHourlyDecay($pet);
                $count++;
            }
        });

        $this->info("Processed {$count} pets with hourly decay.");

        return Command::SUCCESS;
    }

    private function applyHourlyDecay(Pet $pet): void
    {
        $lastDecay = $pet->last_decay_at ?? $pet->created_at;
        $hoursElapsed = (int) floor($lastDecay->diffInMinutes(now()) / 60);

        if ($hoursElapsed < 1) {
            return;
        }

        $hoursElapsed = min($hoursElapsed, self::MAX_HOURS_CAP);
        $decayRates = $this->speciesService->getHourlyDecayValues($pet->species);

        $changes = [];
        $originalStats = [
            'hunger' => $pet->hunger,
            'energy' => $pet->energy,
            'cleanliness' => $pet->cleanliness,
            'health' => $pet->health,
        ];

        $newHunger = min(100, $pet->hunger + ($decayRates['hunger'] * $hoursElapsed));
        $newEnergy = max(0, $pet->energy - ($decayRates['energy'] * $hoursElapsed));
        $newCleanliness = max(0, $pet->cleanliness - ($decayRates['cleanliness'] * $hoursElapsed));
        $newHealth = $pet->health;

        if ($pet->hunger >= 80) {
            $newHealth = max(0, $pet->health - ($decayRates['health'] * $hoursElapsed));
        }

        $pet->update([
            'hunger' => $newHunger,
            'energy' => $newEnergy,
            'cleanliness' => $newCleanliness,
            'health' => $newHealth,
            'last_decay_at' => now(),
        ]);

        $pet->refresh();

        $changes = [
            'hunger' => $newHunger - $originalStats['hunger'],
            'energy' => $newEnergy - $originalStats['energy'],
            'cleanliness' => $newCleanliness - $originalStats['cleanliness'],
            'health' => $newHealth - $originalStats['health'],
        ];

        Event::dispatch(new PetDecayedWhileAway($pet, $hoursElapsed, $changes));

        if ($pet->health <= 0) {
            $pet->update(['is_alive' => false]);
        } else {
            $this->moodService->updateMood($pet);
        }
    }
}