<?php

namespace Tests\Feature\V1;

use App\Models\Pet;
use App\Models\User;
use App\Services\OllamaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetChatTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->pet = Pet::factory()->create([
            'user_id' => $this->user->id,
            'mood' => 'happy',
            'is_alive' => true,
        ]);
        $this->actingAs($this->user);
    }

    public function test_can_chat_with_pet(): void
    {
        $this->mock(OllamaService::class, function ($mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->andReturn('I am happy to see you!');
        });

        $response = $this->postJson("/api/v1/pets/{$this->pet->id}/chat", [
            'message' => 'How are you?',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['reply', 'pet']);
    }

    public function test_cannot_chat_with_other_users_pet(): void
    {
        $otherUser = User::factory()->create();
        $otherPet = Pet::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson("/api/v1/pets/{$otherPet->id}/chat", [
            'message' => 'Hello',
        ]);

        $response->assertForbidden();
    }

    public function test_cannot_chat_with_dead_pet(): void
    {
        $this->pet->update(['is_alive' => false]);

        $response = $this->postJson("/api/v1/pets/{$this->pet->id}/chat", [
            'message' => 'Hello',
        ]);

        $response->assertUnprocessable();
    }

    public function test_validates_message_required(): void
    {
        $response = $this->postJson("/api/v1/pets/{$this->pet->id}/chat", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    public function test_chat_saves_memory(): void
    {
        $this->mock(OllamaService::class, function ($mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->andReturn('Hello!');
        });

        $this->postJson("/api/v1/pets/{$this->pet->id}/chat", [
            'message' => 'Hello friend!',
        ]);

        $this->assertDatabaseHas('pet_memories', [
            'pet_id' => $this->pet->id,
            'content' => 'Hello friend!',
        ]);
    }

    public function test_can_get_pet_memories(): void
    {
        $response = $this->getJson("/api/v1/pets/{$this->pet->id}/memories");

        $response->assertOk()
            ->assertJsonStructure(['data']);
    }
}
