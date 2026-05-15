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
     */
    public function index(): AnonymousResourceCollection
    {
        return PetResource::collection(auth()->user()->pets);
    }

    /**
     * Create a new pet.
     *
     * Creates a new virtual pet for the authenticated user.
     */
    public function store(StorePetRequest $request): JsonResponse
    {
        $user = auth()->user();

        if ($user->pets()->count() >= 10) {
            return response()->json([
                'error' => 'Maximum number of pets reached (10). Delete an existing pet to create a new one.',
            ], 422);
        }

        $pet = $user->pets()->create($request->validated());

        return (new PetResource($pet))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a specific pet.
     *
     * Returns a single pet by ID. Only the pet owner can view it.
     * Updates last_visited_at to track user's return.
     */
    public function show(Pet $pet): PetResource
    {
        Gate::authorize('view', $pet);

        $pet->update(['last_visited_at' => now()]);

        return new PetResource($pet);
    }

    /**
     * Update a pet.
     *
     * Updates a pet's attributes. Only the pet owner can update it.
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
     */
    public function destroy(Pet $pet): JsonResponse
    {
        Gate::authorize('delete', $pet);

        $pet->delete();

        return response()->json(null, 204);
    }
}
