<?php

namespace Tests\Feature\Domain\Pet;
use App\Domain\Pet\Services\PetStateService;
use App\Enums\CleanlinessState;
use App\Enums\EnergyState;
use App\Enums\HealthState;
use App\Enums\HungerState;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetStateServiceTest extends TestCase
{
    use RefreshDatabase;

    private PetStateService $stateService;

    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateService = new PetStateService();

        $user = User::factory()->create();
        $this->pet = Pet::factory()->create([
            'user_id' => $user->id,
            'species' => 'blobcat',
            'health' => 100,
            'energy' => 100,
            'hunger' => 0,
            'cleanliness' => 100,
        ]);
    }

    public function test_hunger_state_full_when_value_0(): void
    {
        $this->pet->update(['hunger' => 0]);
        $this->assertEquals(HungerState::Full, $this->stateService->getHungerState($this->pet));
    }

    public function test_hunger_state_satisfied_when_value_30(): void
    {
        $this->pet->update(['hunger' => 30]);
        $this->assertEquals(HungerState::Satisfied, $this->stateService->getHungerState($this->pet));
    }

    public function test_hunger_state_hungry_when_value_60(): void
    {
        $this->pet->update(['hunger' => 60]);
        $this->assertEquals(HungerState::Hungry, $this->stateService->getHungerState($this->pet));
    }

    public function test_hunger_state_starving_when_value_90(): void
    {
        $this->pet->update(['hunger' => 90]);
        $this->assertEquals(HungerState::Starving, $this->stateService->getHungerState($this->pet));
    }

    public function test_energy_state_exhausted_when_value_15(): void
    {
        $this->pet->update(['energy' => 15]);
        $this->assertEquals(EnergyState::Exhausted, $this->stateService->getEnergyState($this->pet));
    }

    public function test_energy_state_energetic_when_value_90(): void
    {
        $this->pet->update(['energy' => 90]);
        $this->assertEquals(EnergyState::Energetic, $this->stateService->getEnergyState($this->pet));
    }

    public function test_health_state_critical_when_value_15(): void
    {
        $this->pet->update(['health' => 15]);
        $this->assertEquals(HealthState::Critical, $this->stateService->getHealthState($this->pet));
    }

    public function test_cleanliness_state_dirty_when_value_25(): void
    {
        $this->pet->update(['cleanliness' => 25]);
        $this->assertEquals(CleanlinessState::Dirty, $this->stateService->getCleanlinessState($this->pet));
    }

    public function test_get_state_summary_returns_all_states(): void
    {
        $summary = $this->stateService->getStateSummary($this->pet);

        $this->assertArrayHasKey('hunger', $summary);
        $this->assertArrayHasKey('energy', $summary);
        $this->assertArrayHasKey('cleanliness', $summary);
        $this->assertArrayHasKey('health', $summary);
    }

    public function test_to_prompt_context_returns_readable_string(): void
    {
        $context = $this->stateService->toPromptContext($this->pet);

        $this->assertIsString($context);
        $this->assertStringContainsString('full', $context);
        $this->assertStringContainsString('energy', $context);
    }
}
