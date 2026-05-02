<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PetActionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\PetResource;
use App\Models\Pet;
use App\Services\PetActionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Pet action endpoints for interacting with virtual pets.
 *
 * Actions include: feed, play, sleep, clean, heal, talk.
 *
 * @group Pet Actions
 */
class PetActionController extends Controller
{
    public function __construct(
        private PetActionManager $actionManager
    ) {}

    /**
     * Execute a pet action.
     *
     * Performs an action on a pet (feed, play, sleep, clean, heal, talk).
     * Each action modifies pet stats according to species modifiers.
     *
     *
     * @bodyParam type string required The action type (feed, play, sleep, clean, heal, talk)
     * @bodyParam payload object optional Action-specific payload. For feed: { "food": "apple" }. For talk: { "message": "Hello!" }
     *
     * @response 200 {
     *   "message": "Action executed successfully",
     *   "effects": { "hunger": -20, "mood": 5 },
     *   "pet": { ... }
     * }
     */
    public function execute(Request $request, Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $type = PetActionType::tryFrom($request->input('type'));

        if (! $type) {
            return response()->json([
                'error' => 'Invalid action type',
                'valid_types' => array_column(PetActionType::cases(), 'value'),
            ], 422);
        }

        if (! $pet->is_alive) {
            return response()->json([
                'error' => 'Pet is dead and cannot perform actions',
            ], 422);
        }

        $payload = $request->input('payload', []);

        if (! $type->validatePayload($payload)) {
            return response()->json([
                'error' => 'Invalid payload for action type',
            ], 422);
        }

        $effects = $this->actionManager->execute($pet, $type, $payload);

        return response()->json([
            'message' => 'Action executed successfully',
            'effects' => $effects,
            'pet' => new PetResource($pet->fresh()),
        ]);
    }

    /**
     * Get action history for a pet.
     *
     * Returns a list of all actions performed on a pet.
     */
    public function history(Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $actions = $pet->actions()->latest()->get();

        return response()->json([
            'data' => $actions,
        ]);
    }
}
