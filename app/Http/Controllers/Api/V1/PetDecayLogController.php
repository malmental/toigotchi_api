<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetDecayLogResource;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

/**
 * Retrieve decay history for a pet.
 *
 * Returns up to 20 decay log entries from the last 24 hours.
 * Each entry shows stats lost during scheduled hourly decay.
 *
 * @group Decay Logs
 */
class PetDecayLogController extends Controller
{
    /**
     * Get decay logs for a pet.
     *
     * Returns the pet's decay history showing stats lost while away.
     * Logs are retained for 24 hours and limited to 20 most recent entries.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "pet_id": 1,
     *       "hours_elapsed": 3,
     *       "changes": { "hunger": 45, "energy": -30 },
     *       "created_at": "2026-05-11T12:00:00Z"
     *     }
     *   ]
     * }
     */
    public function index(Request $request, Pet $pet): AnonymousResourceCollection|JsonResponse
    {
        Gate::authorize('view', $pet);

        $logs = $pet->decayLogs()
            ->where('created_at', '>=', now()->subDay())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return PetDecayLogResource::collection($logs);
    }
}