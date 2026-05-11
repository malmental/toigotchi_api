<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Manage pet action quota limits.
 *
 * Each pet allows a maximum number of actions per hour window.
 *
 * @group Action Quota
 */
class PetQuotaController extends Controller
{
    /**
     * Get current action quota for a pet.
     *
     * Shows used/remaining actions and when the quota window resets.
     * When exhausted, action endpoints return 429 Too Many Requests.
     *
     * @response 200 {
     *   "used": 2,
     *   "limit": 3,
     *   "remaining": 1,
     *   "resets_at": "2026-05-11T13:00:00Z",
     *   "is_exhausted": false,
     *   "window_start": "2026-05-11T12:00:00Z"
     * }
     */
    public function show(Pet $pet): JsonResponse
    {
        Gate::authorize('view', $pet);

        $quota = $pet->quota ?? $this->createQuota($pet);

        return response()->json([
            'used' => $quota->used_count,
            'limit' => (int) env('PET_ACTION_QUOTA_LIMIT', 3),
            'remaining' => $quota->remaining,
            'resets_at' => $quota->resets_at?->toIso8601String(),
            'is_exhausted' => ! $quota->canPerformAction(),
            'window_start' => $quota->window_start?->toIso8601String(),
        ]);
    }

    private function createQuota(Pet $pet): \App\Models\PetQuota
    {
        return \App\Models\PetQuota::create([
            'pet_id' => $pet->id,
            'used_count' => 0,
            'window_start' => now(),
        ]);
    }
}