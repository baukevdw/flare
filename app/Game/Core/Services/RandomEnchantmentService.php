<?php

namespace App\Game\Core\Services;


use App\Flare\Builders\RandomAffixGenerator;
use App\Flare\Models\Character;
use App\Flare\Models\Item;
use App\Flare\Models\Item as ItemModel;
use App\Flare\Values\ItemEffectsValue;
use App\Flare\Values\RandomAffixDetails;
use Illuminate\Support\Collection;

class RandomEnchantmentService {

    private $randomAffixGenerator;

    public function __construct(RandomAffixGenerator $randomAffixGenerator) {
        $this->randomAffixGenerator = $randomAffixGenerator;
    }

    public function generateForType(Character $character, string $type): Item {
        switch ($type) {
            case 'medium':
                return $this->generateRandomAffixForRandom($character, RandomAffixDetails::MEDIUM);
            case 'legendary':
                return $this->generateRandomAffixForRandom($character, RandomAffixDetails::LEGENDARY);
            case 'basic':
            default:
                return $this->generateRandomAffixForRandom($character, RandomAffixDetails::BASIC);
        }
    }

    public function getCost(string $type): int {
        switch ($type) {
            case 'medium':
                return RandomAffixDetails::MEDIUM;
            case 'legendary':
                return RandomAffixDetails::LEGENDARY;
            case 'basic':
            default:
                return RandomAffixDetails::BASIC;
        }
    }

    public function fetchUniquesFromCharactersInventory(Character $character): Collection {
        return $character->inventory->slots->filter(function($slot) {
            if (!$slot->equipped && ($slot->item->type !== 'quest' && $slot->item->type !== 'alchemy' && $slot->item->type !== 'trinket')) {
                if (!is_null($slot->item->itemPrefix)) {
                    if ($slot->item->itemPrefix->randomly_generated) {
                        return $slot;
                    }
                }
            }

            if (!$slot->equipped && ($slot->item->type !== 'quest' && $slot->item->type !== 'alchemy' && $slot->item->type !== 'trinket')) {
                if (!is_null($slot->item->itemSuffix)) {
                    if ($slot->item->itemSuffix->randomly_generated) {
                        return $slot;
                    }
                }
            }
        })->values();
    }

    public function fetchDataForApi(Character $character): array {
        $uniqueSlots    = $this->fetchUniquesFromCharactersInventory($character);
        $nonUniqueSlots = $this->fetchNonUniqueItems($character);

        return [
            'unique_slots'     => $uniqueSlots,
            'non_unique_slots' => $nonUniqueSlots,
        ];
    }

    public function fetchNonUniqueItems(Character $character): Collection {
        return $character->inventory->slots->filter(function($slot) {
            if (!$slot->equipped && $slot->item->type !== 'quest' && $slot->item->type !== 'alchemy' && $slot->item->type !== 'trinket') {
                if (!is_null($slot->item->itemPrefix)) {
                    if (!$slot->item->itemPrefix->randomly_generated) {
                        return $slot;
                    }
                }

                if (!is_null($slot->item->itemSuffix)) {
                    if (!$slot->item->itemSuffix->randomly_generated) {
                        return $slot;
                    }
                }

                return $slot;
            }
        })->values();
    }

    public function isPlayerInHell(Character $character): bool {
        return $character->inventory->slots->filter(function($slot) {
            return $slot->item->effect === ItemEffectsValue::QUEEN_OF_HEARTS;
        })->isNotEmpty() && $character->map->gameMap->mapType()->isHell();
    }

    protected function generateRandomAffixForRandom(Character $character, int $amount): Item {
        $item = ItemModel::whereNull('item_prefix_id')
            ->whereNull('item_suffix_id')
            ->whereNotIn('type', ['alchemy', 'quest', 'trinket'])
            ->where('cost', '<=', 4000000000)
            ->inRandomOrder()
            ->first();

        $randomAffix = $this->randomAffixGenerator
            ->setCharacter($character)
            ->setPaidAmount($amount);

        $duplicateItem = $item->duplicate();

        $duplicateItem->update([
            'item_prefix_id' => $randomAffix->generateAffix('prefix')->id,
        ]);

        if (rand(1, 100) > 50) {
            $duplicateItem->update([
                'item_suffix_id' => $randomAffix->generateAffix('suffix')->id
            ]);
        }

        $duplicateItem->update([
            'market_sellable' => true,
        ]);

        return $duplicateItem;
    }
}
