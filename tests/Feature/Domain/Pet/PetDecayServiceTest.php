<?php

namespace Tests\Feature\Domain\Pet;
use App\Domain\Pet\Services\PetDecayService;
use App\Domain\Pet\Services\PetMoodService;
use App\Domain\Pet\Services\PetStatBoundaryService;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetDecayServiceTest extends TestCase
{
    use RefreshDatabase;

    private PetDecayService $decayService;

    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decayService = new PetDecayService(
            new PetStatBoundaryService(),
            new PetMoodService()
        );

        $user = User::factory()->create();
        $this->pet = Pet::factory()->create([
            'user_id' => $user->id,
            'health' => 100,
            'energy' => 100,
            'hunger' => 0,
            'cleanliness' => 100,
            'mood' => 'happy',
            'is_alive' => true,
        ]);
    }

    public function test_decay_increases_hunger_by_5(): void
    {
        $this->decayService->applyDecay($this->pet);

        $this->pet->refresh();

        $this->assertEquals(5, $this->pet->hunger);
    }

    public function test_decay_decreases_energy_by_3(): void
    {
        $this->decayService->applyDecay($this->pet);

        $this->pet->refresh();

        $this->assertEquals(97, $this->pet->energy);
    }

    public function test_decay_decreases_cleanliness_by_2(): void
    {
        $this->decayService->applyDecay($this->pet);

        $this->pet->refresh();

        $this->assertEquals(98, $this->pet->cleanliness);
    }

    public function test_decay_reduces_health_when_hunger_high(): void
    {
        $this->pet->update(['hunger' => 80]);

        $this->decayService->applyDecay($this->pet);

        $this->pet->refresh();

        $this->assertEquals(99, $this->pet->health);
    }

    public function test_stats_are_clamped_between_0_and_100(): void
    {
        $this->pet->update([
            'hunger' => 99,
            'energy' => 1,
            'cleanliness' => 1,
        ]);

        $this->decayService->applyDecay($this->pet);
        $this->pet->refresh();
        $this->assertEquals(100, $this->pet->hunger);
        $this->assertEquals(0, $this->pet->energy);
        $this->assertEquals(0, $this->pet->cleanliness);
    }

    public function test_pet_dies_when_hunger_reaches_100(): void
    {
        $this->pet->update(['hunger' => 95]);
        $this->decayService->applyDecay($this->pet);
        $this->decayService->applyDecay($this->pet);
        $this->pet->refresh();
        $this->assertFalse($this->pet->is_alive);
    }

    public function test_pet_dies_when_health_reaches_0(): void
    {
        $this->pet->update(['health' => 1, 'hunger' => 80]);
        $this->decayService->applyDecay($this->pet);
        $this->pet->refresh();
        $this->assertFalse($this->pet->is_alive);
    }

    public function test_process_all_live_pets_returns_count(): void
    {
        $user = User::factory()->create();
        Pet::factory()->count(3)->create([
            'user_id' => $user->id,
            'is_alive' => true,
        ]);

        $count = $this->decayService->processAllLivePets();
        $this->assertEquals(4, $count);
    }
}
