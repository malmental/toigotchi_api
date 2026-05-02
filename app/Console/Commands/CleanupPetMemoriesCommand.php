<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Pet;
use Illuminate\Console\Command;

class CleanupPetMemoriesCommand extends Command
{
    protected $signature = 'pets:cleanup-memories';

    protected $description = 'Remove old pet memories beyond retention limit';

    private const MAX_MEMORIES_PER_PET = 100;

    public function handle(): int
    {
        $totalDeleted = 0;

        Pet::withCount('memories')->get()->each(function (Pet $pet) use (&$totalDeleted) {
            $memoryCount = $pet->memories_count;

            if ($memoryCount > self::MAX_MEMORIES_PER_PET) {
                $toDelete = $memoryCount - self::MAX_MEMORIES_PER_PET;

                $deleted = $pet->memories()
                    ->orderBy('importance', 'desc')
                    ->orderBy('created_at', 'asc')
                    ->skip(self::MAX_MEMORIES_PER_PET)
                    ->take($toDelete)
                    ->delete();

                $totalDeleted += $deleted;

                $this->info("Pet {$pet->name} (ID: {$pet->id}): removed {$deleted} memories");
            }
        });

        $this->info("Cleanup complete. Total memories removed: {$totalDeleted}");

        return Command::SUCCESS;
    }
}
