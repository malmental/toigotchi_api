<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    public function test_can_create_a_pet(): void
    {
        $response = $this->postJson('/api/v1/pets', [
            'name' => 'Mochi',
            'species' => 'blobcat',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('pets', ['name' => 'Mochi']);
    }

    public function test_can_list_pets(): void
    {
        Pet::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/pets');

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_show_a_pet(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/pets/{$pet->id}");

        $response->assertOk()->assertJsonPath('data.name', $pet->name);
    }

    public function test_can_update_a_pet(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patchJson("/api/v1/pets/{$pet->id}", [
            'name' => 'Pepito',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'Pepito']);
    }

    public function test_can_delete_a_pet(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/pets/{$pet->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
    }

    public function test_validates_required_name(): void
    {
        $response = $this->postJson('/api/v1/pets', ['species' => 'blobcat']);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name']);
    }

    public function test_validates_species_must_be_valid(): void
    {
        $response = $this->postJson('/api/v1/pets', ['name' => 'Mochi', 'species' => 'invalid']);

        $response->assertUnprocessable()->assertJsonValidationErrors(['species']);
    }

    public function test_cannot_see_another_users_pet(): void
    {
        $otherUser = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/v1/pets/{$pet->id}");

        $response->assertForbidden();
    }
}
