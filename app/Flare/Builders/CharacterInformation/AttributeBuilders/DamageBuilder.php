<?php

namespace App\Flare\Builders\CharacterInformation\AttributeBuilders;


use App\Flare\Models\Character;
use Illuminate\Support\Collection;

class DamageBuilder extends BaseAttribute {

    /**
     * @var ClassRanksWeaponMasteriesBuilder $classRanksWeaponMasteriesBuilder
     */
    private ClassRanksWeaponMasteriesBuilder $classRanksWeaponMasteriesBuilder;

    /**
     * @param ClassRanksWeaponMasteriesBuilder $classRanksWeaponMasteriesBuilder
     */
    public function __construct(ClassRanksWeaponMasteriesBuilder $classRanksWeaponMasteriesBuilder) {
        $this->classRanksWeaponMasteriesBuilder = $classRanksWeaponMasteriesBuilder;
    }

    /**
     * @inheritDoc
     * @param Character $character
     * @param Collection $skills
     * @param Collection|null $inventory
     * @return void
     */
    public function initialize(Character $character, Collection $skills, ?Collection $inventory): void {
        parent::initialize($character, $skills, $inventory);

        $this->classRanksWeaponMasteriesBuilder->initialize($this->character, $this->skills, $this->inventory);
    }

    /**
     * Build weapon damage.
     *
     * @param float $damageStat
     * @param bool $voided
     * @param string $position
     * @return float
     */
    public function buildWeaponDamage(float $damageStat, bool $voided = false, string $position = 'both'): float {
        $class      = $this->character->class;
        $baseDamage = $damageStat * .05;
        $baseDamage = $baseDamage < 1 ? 1 : $baseDamage;

        $itemDamage      = $this->getDamageFromItems('weapon', $position);
        $skillPercentage = 0;

        if ($this->shouldIncludeSkillDamage($class,'weapon')) {
            $skillPercentage = $this->fetchBaseAttributeFromSkills('base_damage');
        }

        $totalDamage = $baseDamage + $itemDamage;

        if ($voided) {
            return $totalDamage + $totalDamage * $skillPercentage;
        }

        $affixPercentage         = $this->getAttributeBonusFromAllItemAffixes('base_damage');
        $weaponMasteryPercentage = $this->classRanksWeaponMasteriesBuilder->determineBonusForWeapon($position);

        return $totalDamage + $totalDamage * ($skillPercentage + $affixPercentage + $weaponMasteryPercentage);
    }

    /**
     * Build ring damage.
     *
     * @return int
     */
    public function buildRingDamage(): int {
        return $this->getDamageFromItems('ring', 'both');
    }

    /**
     * Build spell damage.
     *
     * @param float $damageStat
     * @param bool $voided
     * @param $position
     * @return float
     */
    public function buildSpellDamage(float $damageStat, bool $voided = false, $position = 'both'): float {
        $class = $this->character->class;

        if ($class->type()->isCaster()) {
            $baseDamage = $damageStat * 0.05;
            $baseDamage = max($baseDamage, 5);
        } else {
            $baseDamage = 0;
        }

        $itemDamage      = $this->getDamageFromItems('spell-damage', $position);

        $skillPercentage = 0;

        if ($this->shouldIncludeSkillDamage($class,'spell')) {
            $skillPercentage = $this->fetchBaseAttributeFromSkills('base_damage');
        }

        $totalDamage = $baseDamage + $itemDamage;

        if ($voided) {
            return $totalDamage + $totalDamage * $skillPercentage;
        }

        $affixPercentage = $this->getAttributeBonusFromAllItemAffixes('base_damage');

        $spellMasteryPercentage = $this->classRanksWeaponMasteriesBuilder->determineBonusForSpellDamage($position);

        return $totalDamage + $totalDamage * ($skillPercentage + $affixPercentage + $spellMasteryPercentage);
    }

    /**
     * Build stacking affix damage.
     *
     * @param bool $voided
     * @return int
     */
    public function buildAffixStackingDamage(bool $voided = false): int {

        if ($voided || is_null($this->inventory)) {
            return 0;
        }

        $itemSuffix = $this->inventory->where('item.itemSuffix.damage_can_stack', true)->sum('item.itemSuffix.damage');
        $itemPrefix = $this->inventory->where('item.itemPrefix.damage_can_stack', true)->sum('item.itemPrefix.damage');

        return $itemSuffix + $itemPrefix;
    }

    /**
     * Build affix non stacking damage.
     *
     * @param bool $voided
     * @return int
     */
    public function buildAffixNonStackingDamage(bool $voided = false): int {

        if ($voided || is_null($this->inventory)) {
            return 0;
        }

        $itemSuffix = $this->inventory->where('item.itemSuffix.damage_can_stack', false)->sum('item.itemSuffix.damage');
        $itemPrefix = $this->inventory->where('item.itemPrefix.damage_can_stack', false)->sum('item.itemPrefix.damage');

        $amounts = array_filter([$itemPrefix, $itemSuffix]);

        if (empty($amounts)) {
            return 0.0;
        }

        return max($amounts);
    }

    /**
     * Build irresistible stacking damage.
     *
     * @param bool $voided
     * @return int
     */
    public function buildIrresistibleNonStackingAffixDamage(bool $voided = false): int {

        if ($voided || is_null($this->inventory)) {
            return 0;
        }

        $itemSuffix = $this->inventory->where('item.itemSuffix.damage_can_stack', false)
                                      ->where('item.itemSuffix.irresistible_damage', true)
                                      ->sum('item.itemSuffix.damage');
        $itemPrefix = $this->inventory->where('item.itemPrefix.damage_can_stack', false)
                                      ->where('item.itemPrefix.irresistible_damage', true)
                                      ->sum('item.itemPrefix.damage');

        $amounts = array_filter([$itemPrefix, $itemSuffix]);

        if (empty($amounts)) {
            return 0.0;
        }

        return max($amounts);
    }

    /**
     * Build non stacking irresistible damage.
     *
     * @param bool $voided
     * @return int
     */
    public function buildIrresistibleStackingAffixDamage(bool $voided = false): int {

        if ($voided || is_null($this->inventory)) {
            return 0;
        }

        $itemSuffix = $this->inventory->where('item.itemSuffix.damage_can_stack', true)
                                      ->where('item.itemSuffix.irresistible_damage', true)
                                      ->sum('item.itemSuffix.damage');
        $itemPrefix = $this->inventory->where('item.itemPrefix.damage_can_stack', true)
                                      ->where('item.itemPrefix.irresistible_damage', true)
                                      ->sum('item.itemPrefix.damage');

        return $itemPrefix + $itemSuffix;
    }

    /**
     * Build life stealing.
     *
     * @param bool $voided
     * @return float
     */
    public function buildLifeStealingDamage(bool $voided = false): float {

        if ($voided || is_null($this->inventory)) {
            return 0;
        }

        $class = $this->character->class;

        if ($class->type()->isVampire()) {
            $itemSuffix = $this->inventory->sum('item.itemSuffix.steal_life_amount');
            $itemPrefix = $this->inventory->sum('item.itemPrefix.steal_life_amount');

            $lifeStealAmount  = $itemSuffix + $itemPrefix;
            $gameMap          = $this->character->map->gameMap;

            if ($lifeStealAmount >= 1) {
                $lifeStealAmount =  0.99;
            }

            if (($gameMap->mapType()->isHell() || $gameMap->mapType()->isPurgatory())) {
                $lifeStealAmount = $lifeStealAmount / 2;
            }

            return $lifeStealAmount;
        }

        $lifeStealAmounts = [
            $this->inventory->max('item.itemSuffix.steal_life_amount'),
            $this->inventory->max('item.itemPrefix.steal_life_amount'),
        ];

        $lifeStealAmounts = array_filter($lifeStealAmounts);

        if (empty($lifeStealAmounts)) {
            return 0;
        }

        $value = max($lifeStealAmounts);

        return $value >= 1 ? .99 : $value;
    }
}
