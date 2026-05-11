<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetDecayLogResource;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PetDecayLogController extends Controller
{
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