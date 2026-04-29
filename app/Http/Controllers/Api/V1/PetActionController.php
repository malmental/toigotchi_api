<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\PetResource;
use App\Enums\PetActionType;
use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Services\PetActionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetActionController extends Controller
{
    public function __construct(
        private PetActionManager $actionManager
    ) {}

    public function execute(Request $request, Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $type = PetActionType::tryFrom($request->input('type'));

        if (!$type) {
            return response()->json([
                'error' => 'Invalid action type',
                'valid_types' => array_column(PetActionType::cases(), 'value'),
            ], 422);
        }

        if (!$pet->is_alive) {
            return response()->json([
                'error' => 'Pet is dead and cannot perform actions',
            ], 422);
        }

        $payload = $request->input('payload', []);

        if (!$type->validatePayload($payload)) {
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

    public function history(Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $actions = $pet->actions()->latest()->get();

        return response()->json([
            'data' => $actions,
        ]);
    }
}