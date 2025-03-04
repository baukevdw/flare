<?php

namespace Tests\Unit\Game\Mercenaries\Values;

use App\Game\Mercenaries\Values\MercenaryValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateItem;

class MercenaryValueTest extends TestCase {

    use RefreshDatabase, CreateItem;


    public function setUp(): void {
        parent::setUp();
    }

    public function tearDown(): void {
        parent::tearDown();
    }

    public function testGetMaxBonusWithNoReincarnation() {

        $mercType = new MercenaryValue(MercenaryValue::CHILD_OF_GOLD_DUST);

        $bonus = $mercType->getMaxBonus();

        $this->assertEquals(1, $bonus);
    }

    public function testgetMaxBonusWithReincarnation() {
        $mercType = new MercenaryValue(MercenaryValue::CHILD_OF_GOLD_DUST);

        $bonus = $mercType->getMaxBonus(1);

        $this->assertEquals(2, $bonus);
    }
}
