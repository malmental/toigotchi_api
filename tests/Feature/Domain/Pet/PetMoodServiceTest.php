<?php

namespace Tests\Feature\Domain\Pet;

use App\Domain\Pet\Services\PetMoodService;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetMoodServiceTest extends TestCase
{
    use RefreshDatabase;

    private PetMoodService $moodService;

    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moodService = new PetMoodService;

        $user = User::factory()->create();

        $this->pet = Pet::factory()->create([
            'user_id' => $user->id,
            'health' => 100,
            'energy' => 100,
            'hunger' => 0,
            'cleanliness' => 100,
        ]);
    }

    public function test_mood_is_happy_when_all_stats_good(): void
    {
        $mood = $this->moodService->calculateMood($this->pet);
        $this->assertEquals('happy', $mood);
    }

    public function test_mood_is_angry_when_hunger_high(): void
    {
        $this->pet->update(['hunger' => 80]);
        $mood = $this->moodService->calculateMood($this->pet);
        $this->assertEquals('angry', $mood);
    }

    public function test_mood_is_angry_when_health_low(): void
    {
        $this->pet->update(['health' => 20]);
        $mood = $this->moodService->calculateMood($this->pet);
        $this->assertEquals('angry', $mood);
    }

    public function test_mood_is_tired_when_energy_low(): void
    {
        $this->pet->update(['energy' => 20]);
        $mood = $this->moodService->calculateMood($this->pet);
        $this->assertEquals('tired', $mood);
    }

    public function test_mood_is_dirty_when_cleanliness_low(): void
    {
        $this->pet->update(['cleanliness' => 30]);
        $mood = $this->moodService->calculateMood($this->pet);
        $this->assertEquals('dirty', $mood);
    }

    public function test_mood_is_neutral_by_default(): void
    {
        $this->pet->update([
            'health' => 50,
            'energy' => 50,
            'hunger' => 50,
            'cleanliness' => 50,
        ]);

        $mood = $this->moodService->calculateMood($this->pet);
        $this->assertEquals('neutral', $mood);
    }

    public function test_update_mood_saves_to_pet(): void
    {
        $this->pet->update(['hunger' => 90]);
        $this->moodService->updateMood($this->pet);
        $this->pet->refresh();
        $this->assertEquals('angry', $this->pet->mood);
    }
}
