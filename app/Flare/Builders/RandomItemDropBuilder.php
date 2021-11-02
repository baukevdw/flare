<?php

namespace App\Flare\Builders;

use Illuminate\Database\Eloquent\Collection;
use App\Flare\Models\Item;
use App\Flare\Models\ItemAffix;

class RandomItemDropBuilder {

    /**
     * @var Collection $itemAffixes
     */
    private $itemAffixes;

    private $monsterPlane;

    private $characterLevel = 0;

    private $monsterLevel   = 0;

    /**
     * Set the item affixes
     *
     * @param Colletion $itemAffixes
     * @return RandomItemDropBuilder
     */
    public function setItemAffixes(Collection $itemAffixes): RandomItemDropBuilder {
        $this->itemAffixes = $itemAffixes;

        return $this;
    }

    /**
     * Set the monster plane.
     *
     * @param string $name
     * @return $this
     */
    public function setMonsterPlane(string $name): RandomItemDropBuilder {
        $this->monsterPlane = $name;

        return $this;
    }

    /**
     * set the character level.
     *
     * @param int $level
     * @return $this
     */
    public function setCharacterLevel(int $level): RandomItemDropBuilder {
        $this->characterLevel = $level;

        return $this;
    }

    /**
     * set the monster level.
     *
     * @param int $level
     * @return $this
     */
    public function setMonsterMaxLevel(int $level): RandomItemDropBuilder {
        $this->monsterLevel = $level;

        return $this;
    }

    /**
     * Generate an item.
     *
     * This will generate a random item.
     *
     * We start by fetching a random item with prefixes and suffixes., we then duplicate the item and fetch a random affix.
     * From that we check if the affix is the same on the item - if it is, attach it, if not, check if its the same, if it is, delete the
     * duplicate and return the item in question - or attach the new affix and pass that back.
     *
     * Based on the monsters plane, if it is Shadow Plane, then any item of any value and any affix can drop, so long as the
     * monster is at least 10 levels higher than the player.
     *
     * @return Item
     */
    public function generateItem(): Item {
        $item          = $this->getItem();
        $duplicateItem = $this->duplicateItem($item);
        $affix         = $this->fetchRandomItemAffix();

        if (!is_null($duplicateItem->itemSuffix) || !is_null($duplicateItem->itemPrefix)) {
            $duplicateItem = $this->attachAffixOrDelete($duplicateItem, $affix);
        } else {
            $duplicateItem = $this->attachAffixOrDelete($duplicateItem, $affix);
        }

        if (is_null($duplicateItem)) {
            return $item;
        }

        $duplicateItem->update([
            'market_sellable' => true,
        ]);

        return $duplicateItem->refresh();
    }

    protected function getItem(): Item {
        $query =  Item::inRandomOrder()->with(['itemSuffix', 'itemPrefix'])
                                    ->whereNotIn('type', ['artifact', 'quest', 'alchemy']);


        $totalLevels = $this->monsterLevel - $this->characterLevel;

        if ($this->monsterPlane !== 'Shadow Plane' && !($totalLevels >= 10)) {
            $query = $query->where('can_drop', true);
        }

        return $query->get()->first();
    }

    protected function attachAffixOrDelete(Item $duplicateItem, ItemAffix $affix) {
        if ($this->hasSameAffix($duplicateItem, $affix)) {
            $duplicateItem->delete();
        } else {
            return $this->attachAffix($duplicateItem, $affix);
        }

        return null;
    }

    protected function duplicateItem(Item $item): Item {
        $duplicateItem = $item->duplicate();

        return $duplicateItem->refresh()->load(['itemSuffix', 'itemPrefix']);
    }

    protected function hasSameAffix(Item $duplicateItem, ItemAffix $affix): bool {
        $item = Item::where('item_' . $affix->type . '_id', $affix->id)->where('name', $duplicateItem->name)->first();

        return !is_null($item);
    }

    protected function attachAffix(Item $item, ItemAffix $itemAffix): Item {
        $item->update(['item_'.$itemAffix->type.'_id' => $itemAffix->id]);

        if ($itemAffix->type === 'suffix') {
            if (!is_null($item->itemPrefix)) {
                $affixes = array_values($this->itemAffixes->where('type', 'prefix')->all());

                $item->update(['item_prefix_id' => $affixes[rand(0, count($affixes) - 1)]->id]);
            }
        }

        if ($itemAffix->type === 'prefix') {
            if (!is_null($item->itemSuffix)) {
                $affixes = array_values($this->itemAffixes->where('type', 'suffix')->all());

                $item->update(['item_suffix_id' => $affixes[rand(0, count($affixes) - 1)]->id]);
            }
        }

        return $item->refresh();
    }

    protected function fetchRandomItemAffix(): ItemAffix {

        $totalLevels = $this->monsterLevel - $this->characterLevel;

        if ($this->monsterPlane === 'Shadow Plane' && $totalLevels >= 10) {
            return ItemAffix::inRandomOrder()->first();
        }

        $index = count($this->itemAffixes) - 1;

        return $this->itemAffixes[rand(0, $index)];
    }
}