<?php

namespace Tests\Feature\Domain\Pet;
use App\Domain\Pet\Services\SpeciesModifierService;
use Tests\TestCase;

class SpeciesModifierServiceTest extends TestCase
{
    private SpeciesModifierService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SpeciesModifierService();
    }

    public function test_blobcat_has_default_decay_rates(): void
    {
        $this->assertEquals(5, $this->service->getHungerDecayRate('blobcat'));
        $this->assertEquals(2, $this->service->getCleanlinessDecayRate('blobcat'));
    }

    public function test_foxkid_has_slower_hunger_decay(): void
    {
        $this->assertEquals(4, $this->service->getHungerDecayRate('foxkid'));
        $this->assertLessThan(
            $this->service->getHungerDecayRate('blobcat'),
            $this->service->getHungerDecayRate('foxkid')
        );
    }

    public function test_foxkid_has_higher_happiness_boost(): void
    {
        $this->assertEquals(1.5, $this->service->getHappinessBoostRate('foxkid'));
        $this->assertGreaterThan(
            $this->service->getHappinessBoostRate('blobcat'),
            $this->service->getHappinessBoostRate('foxkid')
        );
    }

    public function test_draggle_has_better_food_efficiency(): void
    {
        $this->assertEquals(0.8, $this->service->getFoodEfficiency('draggle'));
        $this->assertLessThan(
            $this->service->getFoodEfficiency('blobcat'),
            $this->service->getFoodEfficiency('draggle')
        );
    }

    public function test_draggle_has_slower_decay_rates(): void
    {
        $this->assertEquals(3, $this->service->getHungerDecayRate('draggle'));
        $this->assertEquals(1, $this->service->getCleanlinessDecayRate('draggle'));
    }

    public function test_unknown_species_falls_back_to_blobcat(): void
    {
        $this->assertEquals(5, $this->service->getHungerDecayRate('unknown'));
        $this->assertEquals(2, $this->service->getCleanlinessDecayRate('unknown'));
    }

    public function test_get_modifiers_returns_full_array(): void
    {
        $modifiers = $this->service->getModifiers('foxkid');
        $this->assertArrayHasKey('hunger_decay_rate', $modifiers);
        $this->assertArrayHasKey('cleanliness_decay_rate', $modifiers);
        $this->assertArrayHasKey('happiness_boost_rate', $modifiers);
        $this->assertArrayHasKey('food_efficiency', $modifiers);
    }

    public function test_get_decay_values_returns_hunger_and_cleanliness(): void
    {
        $decay = $this->service->getDecayValues('draggle');
        $this->assertArrayHasKey('hunger', $decay);
        $this->assertArrayHasKey('cleanliness', $decay);
        $this->assertEquals(3, $decay['hunger']);
        $this->assertEquals(1, $decay['cleanliness']);
    }
}
