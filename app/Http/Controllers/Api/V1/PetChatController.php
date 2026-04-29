<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Pet\Services\PetPromptBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Pet;
use App\Models\PetMemory;
use App\Services\OllamaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PetChatController extends Controller
{
    public function __construct(
        private OllamaService $ollama,
        private PetPromptBuilder $promptBuilder,
    ) {}

    public function chat(ChatMessageRequest $request, Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        if (!$pet->is_alive) {
            return response()->json([
                'error' => 'Your pet has passed away and cannot chat.',
            ], 422);
        }

        $message = $request->input('message');
        $memories = $pet->memories()->recent(5)->get();

        $prompt = $this->promptBuilder->build($pet, $message, $memories);

        $reply = $this->ollama->chat($prompt);

        PetMemory::create([
            'pet_id' => $pet->id,
            'type' => 'conversation',
            'content' => $message,
            'ai_response' => $reply,
            'importance' => 5,
        ]);

        return response()->json([
            'reply' => $reply,
            'pet' => [
                'id' => $pet->id,
                'name' => $pet->name,
                'mood' => $pet->mood,
            ],
        ]);
    }

    public function memories(Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $memories = $pet->memories()
            ->recent(20)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'content' => $m->content,
                'ai_response' => $m->ai_response,
                'created_at' => $m->created_at,
            ]);

        return response()->json(['data' => $memories]);
    }
}
