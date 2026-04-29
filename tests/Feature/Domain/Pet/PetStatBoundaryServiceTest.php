<?php

namespace Tests\Feature\Domain\Pet;
use App\Domain\Pet\Services\PetStatBoundaryService;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetStatBoundaryServiceTest extends TestCase
{
    use RefreshDatabase;

    private PetStatBoundaryService $boundaryService;

    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boundaryService = new PetStatBoundaryService();
        $user = User::factory()->create();
        $this->pet = Pet::factory()->create([
            'user_id' => $user->id,
        ]);
    }

    public function test_clamps_health_between_0_and_100(): void
    {
        $this->pet->update(['health' => 150]);
        $this->boundaryService->clamp($this->pet);
        $this->pet->refresh();
        $this->assertEquals(100, $this->pet->health);
        $this->pet->update(['health' => -10]);
        $this->boundaryService->clamp($this->pet);
        $this->pet->refresh();
        $this->assertEquals(0, $this->pet->health);
    }

    public function test_is_dead_returns_true_when_hunger_100(): void
    {
        $this->pet->update(['hunger' => 100]);
        $this->assertTrue($this->boundaryService->isDead($this->pet));
    }

    public function test_is_dead_returns_true_when_health_0(): void
    {
        $this->pet->update(['health' => 0]);
        $this->assertTrue($this->boundaryService->isDead($this->pet));
    }

    public function test_is_dead_returns_false_when_alive(): void
    {
        $this->pet->update([
            'hunger' => 50,
            'health' => 50,
        ]);

        $this->assertFalse($this->boundaryService->isDead($this->pet));
    }

    public function test_kill_pet_sets_is_alive_false(): void
    {
        $this->boundaryService->killPet($this->pet);
        $this->pet->refresh();
        $this->assertFalse($this->pet->is_alive);
    }
}
