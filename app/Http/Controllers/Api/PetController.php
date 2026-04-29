<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Http\Resources\PetResource;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PetController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PetResource::collection(auth()->user()->pets);
    }

    public function store(StorePetRequest $request): JsonResponse
    {
        $pet = auth()->user()->pets()->create($request->validated());

        return (new PetResource($pet))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Pet $pet): PetResource
    {
        Gate::authorize('view', $pet);

        return new PetResource($pet);
    }

    public function update(UpdatePetRequest $request, Pet $pet): PetResource
    {
        Gate::authorize('update', $pet);

        $pet->update($request->validated());

        return new PetResource($pet);
    }

    public function destroy(Pet $pet): JsonResponse
    {
        Gate::authorize('delete', $pet);

        $pet->delete();

        return response()->json(null, 204);
    }
}
