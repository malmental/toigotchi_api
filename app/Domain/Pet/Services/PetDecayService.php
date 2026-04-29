<?php

namespace App\Domain\Pet\Services;
use App\Domain\Pet\Events\PetDied;
use App\Domain\Pet\Events\PetStatChanged;
use App\Domain\Pet\ValueObjects\PetStats;
use App\Models\Pet;
use Illuminate\Support\Facades\Event;

class PetDecayService
{
    public function __construct(
        private PetStatBoundaryService $boundaryService,
        private PetMoodService $moodService,
        private SpeciesModifierService $speciesService,
    ) {}

    public function applyDecay(Pet $pet): Pet
    {
        $stats = PetStats::fromPet($pet);
        $decayRates = $this->speciesService->getDecayValues($pet->species);

        $decay = [
            'hunger' => min(100, $stats->hunger + $decayRates['hunger']),
            'energy' => max(0, $stats->energy - 3),
            'cleanliness' => max(0, $stats->cleanliness - $decayRates['cleanliness']),
        ];

        if ($stats->hunger >= 80) {
            $decay['health'] = max(0, $stats->health - 1);
        }

        $pet->update($decay);
        $pet = $this->boundaryService->clamp($pet);

        Event::dispatch(new PetStatChanged($pet, $decay));

        if ($this->boundaryService->isDead($pet)) {
            $this->boundaryService->killPet($pet);
            Event::dispatch(new PetDied($pet));
        } else {
            $pet = $this->moodService->updateMood($pet);
        }

        return $pet->fresh();
    }

    public function processAllLivePets(): int
    {
        $count = 0;

        Pet::where('is_alive', true)->chunkById(100, function ($pets) use (&$count) {
            foreach ($pets as $pet) {
                $this->applyDecay($pet);
                $count++;
            }
        });

        return $count;
    }
}
