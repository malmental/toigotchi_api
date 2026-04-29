<?php

namespace App\Console\Commands;
use App\Domain\Pet\Services\PetDecayService;
use Illuminate\Console\Command;

class UpdatePetStatsCommand extends Command
{
    protected $signature = 'pets:decay';

    protected $description = 'Apply stat decay to all live pets';

    public function __construct(
        private PetDecayService $decayService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->decayService->processAllLivePets();

        $this->info("Processed {$count} pets.");

        return Command::SUCCESS;
    }
}