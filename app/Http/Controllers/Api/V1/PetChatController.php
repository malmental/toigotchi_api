<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Pet\Services\PetPromptBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Pet;
use App\Models\PetMemory;
use App\Services\OllamaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Pet chat endpoints for AI-powered conversations.
 *
 * Integrates with Ollama for natural language interaction.
 * Maintains conversation history as memories for context.
 *
 * @group Pet Chat
 */
class PetChatController extends Controller
{
    public function __construct(
        private OllamaService $ollama,
        private PetPromptBuilder $promptBuilder,
    ) {}

    /**
     * Send a message to a pet.
     *
     * Sends a message to the pet and returns an AI-generated response.
     * The conversation is stored as a memory for future context.
     *
     *
     * @bodyParam message string required The message to send to the pet
     *
     * @response 200 {
     *   "reply": "I love playing with you!",
     *   "pet": { "id": 1, "name": "Mochi", "mood": "happy" }
     * }
     */
    public function chat(ChatMessageRequest $request, Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        if (! $pet->is_alive) {
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

    /**
     * Stream a chat response from the pet.
     *
     * Sends a message and streams the AI response in real-time via SSE.
     * The conversation is stored as a memory after completion.
     */
    public function stream(ChatMessageRequest $request, Pet $pet): StreamedResponse
    {
        Gate::authorize('view', $pet);

        if (! $pet->is_alive) {
            return response()->stream(function () {
                echo "data: {\"error\": \"Your pet has passed away and cannot chat.\"}\n\n";
            }, 422, ['Content-Type' => 'text/event-stream']);
        }

        $message = $request->input('message');
        $memories = $pet->memories()->recent(5)->get();
        $prompt = $this->promptBuilder->build($pet, $message, $memories);
        $fullResponse = '';

        return response()->stream(function () use ($pet, $prompt, $message, &$fullResponse) {
            $this->ollama->chatStream($prompt, function (string $chunk) use (&$fullResponse) {
                $fullResponse .= $chunk;
                echo 'data: {"chunk": '.json_encode($chunk)."}\n\n";
                ob_flush();
                flush();
            });

            echo "data: [DONE]\n\n";
            ob_flush();
            flush();

            PetMemory::create([
                'pet_id' => $pet->id,
                'type' => 'conversation',
                'content' => $message,
                'ai_response' => $fullResponse,
                'importance' => 5,
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Get pet memories.
     *
     * Returns the conversation history and important events for a pet.
     * Used to provide context for AI conversations.
     */
    public function memories(Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $memories = $pet->memories()
            ->recent(20)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'content' => $m->content,
                'ai_response' => $m->ai_response,
                'created_at' => $m->created_at,
            ]);

        return response()->json(['data' => $memories]);
    }
}
