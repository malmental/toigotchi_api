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

/**
 * Pet CRUD endpoints for managing virtual pets.
 *
 * @group Pets
 */
class PetController extends Controller
{
    /**
     * List all pets for the authenticated user.
     *
     * Returns a collection of pets owned by the authenticated user.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return PetResource::collection(auth()->user()->pets);
    }

    /**
     * Create a new pet.
     *
     * Creates a new virtual pet for the authenticated user.
     *
     * @param StorePetRequest $request
     * @return JsonResponse
     */
    public function store(StorePetRequest $request): JsonResponse
    {
        $pet = auth()->user()->pets()->create($request->validated());

        return (new PetResource($pet))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a specific pet.
     *
     * Returns a single pet by ID. Only the pet owner can view it.
     *
     * @param Pet $pet
     * @return PetResource
     */
    public function show(Pet $pet): PetResource
    {
        Gate::authorize('view', $pet);

        return new PetResource($pet);
    }

    /**
     * Update a pet.
     *
     * Updates a pet's attributes. Only the pet owner can update it.
     *
     * @param UpdatePetRequest $request
     * @param Pet $pet
     * @return PetResource
     */
    public function update(UpdatePetRequest $request, Pet $pet): PetResource
    {
        Gate::authorize('update', $pet);

        $pet->update($request->validated());

        return new PetResource($pet);
    }

    /**
     * Delete a pet.
     *
     * Permanently removes a pet. Only the pet owner can delete it.
     *
     * @param Pet $pet
     * @return JsonResponse
     */
    public function destroy(Pet $pet): JsonResponse
    {
        Gate::authorize('delete', $pet);

        $pet->delete();

        return response()->json(null, 204);
    }
}