<?php

namespace Tests\Feature\V1;

use App\Models\Pet;
use App\Models\PetQuota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetQuotaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    public function test_can_perform_action_within_quota(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 0,
            'window_start' => now(),
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pet_quotas', [
            'pet_id' => $pet->id,
            'used_count' => 1,
        ]);
    }

    public function test_returns_429_when_quota_exhausted(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 3,
            'window_start' => now(),
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertStatus(429);
        $response->assertJsonStructure([
            'error',
            'quota' => ['used', 'limit', 'resets_at'],
        ]);
    }

    public function test_headers_include_retry_after_when_quota_exhausted(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 3,
            'window_start' => now(),
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $this->assertNotNull($response->headers->get('Retry-After'));
    }

    public function test_quota_creates_automatically_when_not_exists(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pet_quotas', [
            'pet_id' => $pet->id,
            'used_count' => 1,
        ]);
    }

    public function test_can_perform_multiple_actions_up_to_limit(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 0,
            'window_start' => now(),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
                'type' => 'feed',
                'payload' => ['food' => 'apple'],
            ]);
            $response->assertOk();
        }

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);
        $response->assertStatus(429);
    }

    public function test_quota_resets_when_window_expires(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 3,
            'window_start' => now()->subMinutes(65),
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pet_quotas', [
            'pet_id' => $pet->id,
            'used_count' => 1,
        ]);
    }

    public function test_dead_pet_ignores_quota_check(): void
    {
        $pet = Pet::factory()->create([
            'user_id' => $this->user->id,
            'is_alive' => false,
        ]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 3,
            'window_start' => now(),
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertUnprocessable();
    }

    public function test_get_quota_endpoint(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 2,
            'window_start' => now(),
        ]);

        $response = $this->getJson("/api/v1/pets/{$pet->id}/quota");

        $response->assertOk();
        $response->assertJsonStructure([
            'used',
            'limit',
            'remaining',
            'resets_at',
            'is_exhausted',
            'window_start',
        ]);
        $response->assertJson([
            'used' => 2,
            'limit' => 3,
            'remaining' => 1,
            'is_exhausted' => false,
        ]);
    }

    public function test_get_quota_creates_record_if_not_exists(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/pets/{$pet->id}/quota");

        $response->assertOk();
        $this->assertDatabaseHas('pet_quotas', [
            'pet_id' => $pet->id,
        ]);
    }

    public function test_get_quota_returns_exhausted_when_limit_reached(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 3,
            'window_start' => now(),
        ]);

        $response = $this->getJson("/api/v1/pets/{$pet->id}/quota");

        $response->assertOk();
        $response->assertJson([
            'used' => 3,
            'limit' => 3,
            'remaining' => 0,
            'is_exhausted' => true,
        ]);
    }

    public function test_cannot_access_another_users_pet_quota(): void
    {
        $otherUser = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/v1/pets/{$pet->id}/quota");

        $response->assertForbidden();
    }

    public function test_response_includes_quota_info_after_action(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);
        PetQuota::factory()->create([
            'pet_id' => $pet->id,
            'used_count' => 0,
            'window_start' => now(),
        ]);

        $response = $this->postJson("/api/v1/pets/{$pet->id}/actions", [
            'type' => 'feed',
            'payload' => ['food' => 'apple'],
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'quota' => ['used', 'limit', 'remaining', 'resets_at'],
        ]);
    }
}