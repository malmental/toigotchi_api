<?php

namespace App\Domain\Pet\Services;

use App\Domain\Pet\Services\PetStateService;
use App\Models\Pet;
use App\Models\PetMemory;

class PetPromptBuilder
{
    public function __construct(
        private PetStateService $stateService,
    ) {}

    public function build(Pet $pet, string $userMessage, $memories = []): string
    {
        $stateSummary = $this->stateService->getStateSummary($pet);

        $moodDescription = match ($pet->mood) {
            'happy' => 'happy and cheerful',
            'angry' => 'irritable and upset',
            'tired' => 'sleepy and fatigued',
            'dirty' => 'uncomfortable and dirty',
            default => 'calm and neutral',
        };

        $systemPrompt = $this->buildSystemPrompt($pet, $stateSummary, $moodDescription);
        $memoryContext = $this->buildMemoryContext($memories);

        return $systemPrompt . "\n\n" . $memoryContext . "\n\nUser says: " . $userMessage . "\n\nYour response:";
    }

    private function buildSystemPrompt(Pet $pet, array $stateSummary, string $moodDescription): string
    {
        $speciesDescription = match ($pet->species) {
            'blobcat' => 'a fluffy blob cat creature',
            'foxkid' => 'a playful fox-like creature',
            'draggle' => 'a small dragon-like creature',
            default => 'a mysterious creature',
        };

        return <<<PROMPT
You are {$pet->name}, {$speciesDescription}.

Current state:
- Mood: {$moodDescription}
- {$stateSummary['hunger']['description']}
- {$stateSummary['energy']['description']}
- {$stateSummary['cleanliness']['description']}
- {$stateSummary['health']['description']}

You are a virtual pet with feelings. Respond to the user in a way that reflects your current state and personality. Be concise (1-3 sentences), affectionate, and in character.
PROMPT;
    }

    private function buildMemoryContext($memories): string
    {
        if (empty($memories)) {
            return '';
        }

        $memoryLines = collect($memories)->take(5)->map(function ($memory) {
            return "- {$memory->content}";
        })->join("\n");

        return "Recent memories:\n{$memoryLines}";
    }

    public function getRecentMemories(Pet $pet, int $limit = 5): array
    {
        return $pet->memories()
            ->recent($limit)
            ->get()
            ->toArray();
    }
}
