<?php

namespace Tests\Feature\V1;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetActionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    public function test_can_feed_a_pet(): void
    {
        $pet = Pet::factory()->create([
            'user_id' => $this->user->id,
            'hunger' => 80,
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('pet_actions', [
            'pet_id' => $pet->id,
            'type' => 'feed',
        ]);
    }

    public function test_cannot_feed_another_users_pet(): void
    {
        $otherUser = User::factory()->create();

        $pet = Pet::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertForbidden();
    }

    public function test_validates_invalid_action_type(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'invalid_action',
        ]);

        $response->assertUnprocessable();
    }

    public function test_cannot_interact_with_dead_pet(): void
    {
        $pet = Pet::factory()->create([
            'user_id' => $this->user->id,
            'is_alive' => false,
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertUnprocessable();
    }

    public function test_action_is_logged_to_database(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'play',
        ]);

        $this->assertDatabaseHas('pet_actions', [
            'pet_id' => $pet->id,
            'type' => 'play',
        ]);
    }

    public function test_play_drains_energy(): void
    {
        $pet = Pet::factory()->create([
            'user_id' => $this->user->id,
            'energy' => 100,
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'play',
        ]);

        $response->assertOk();

        $pet->refresh();

        $this->assertEquals(85, $pet->energy);
    }

    public function test_sleep_restores_energy(): void
    {
        $pet = Pet::factory()->create([
            'user_id' => $this->user->id,
            'energy' => 50,
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'sleep',
        ]);

        $response->assertOk();

        $pet->refresh();

        $this->assertEquals(80, $pet->energy);
    }
}
