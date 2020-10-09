<?php

namespace Tests\Unit\Flare\View\Livewire\Admin\Monsters\Partials;

use Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Flare\View\Livewire\Admin\Monsters\Partials\Monster;
use Database\Seeders\GameSkillsSeeder;
use Tests\TestCase;
use Tests\Traits\CreateMonster;

class MonsterTest extends TestCase
{
    use RefreshDatabase, CreateMonster;

    public function setUp(): void {
        parent::setUp();

        $this->seed(GameSkillsSeeder::class);
    }

    public function testUpdateStepIsCalled() {
        $monster = $this->createMonster();

        Livewire::test(Monster::class)->call('updateCurrentStep', 2, $monster)->assertSet('monster', $monster)->assertSet('currentStep', 2);
    }
}
