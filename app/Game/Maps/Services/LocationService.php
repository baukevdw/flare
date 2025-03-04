<?php

namespace App\Game\Maps\Services;

use App\Flare\Builders\Character\CharacterCacheData;
use App\Flare\Events\UpdateCharacterAttackEvent;
use App\Flare\Handlers\UpdateCharacterAttackTypes;
use App\Flare\Jobs\CharacterAttackTypesCacheBuilderWithDeductions;
use App\Flare\Models\Map;
use App\Flare\Values\LocationEffectValue;
use App\Flare\Values\LocationType;
use App\Game\Battle\Events\UpdateCharacterStatus;
use App\Game\Maps\Events\UpdateDuelAtPosition;
use App\Game\Maps\Events\UpdateLocationBasedCraftingOptions;
use App\Game\Maps\Events\UpdateLocationBasedSpecialShops;
use App\Game\Maps\Events\UpdateRankFights;
use Illuminate\Support\Collection;
use Storage;
use League\Fractal\Manager;
use App\Flare\Cache\CoordinatesCache;
use App\Flare\Models\Character;
use App\Flare\Models\Kingdom;
use App\Flare\Models\Location;
use App\Flare\Transformers\KingdomTransformer;
use App\Flare\Models\CelestialFight;
use App\Game\Maps\Services\Common\CanPlayerMassEmbezzle;
use App\Game\Maps\Services\Common\LiveCharacterCount;
use App\Game\Core\Traits\KingdomCache;

class LocationService {

    use KingdomCache, LiveCharacterCount, CanPlayerMassEmbezzle;

    /**
     * @var CoordinatesCache $coordinatesCache
     */
    private CoordinatesCache $coordinatesCache;

    /**
     * @var CharacterCacheData $characterCacheData
     */
    private CharacterCacheData $characterCacheData;

    /**
     * @var UpdateCharacterAttackTypes $updateCharacterAttackTypes
     */
    private UpdateCharacterAttackTypes $updateCharacterAttackTypes;

    /**
     * @var Location $location | null
     */
    private $location;

    /**
     * @var bool $canSettle | false
     */
    private $canSettle = false;

    /**
     * @param CoordinatesCache $coordinatesCache
     * @param CharacterCacheData $characterCacheData
     * @param UpdateCharacterAttackTypes $updateCharacterAttackTypes
     */
    public function __construct(CoordinatesCache $coordinatesCache, CharacterCacheData $characterCacheData, UpdateCharacterAttackTypes $updateCharacterAttackTypes) {
        $this->coordinatesCache           = $coordinatesCache;
        $this->characterCacheData         = $characterCacheData;
        $this->updateCharacterAttackTypes = $updateCharacterAttackTypes;

    }

    /**
     * Get location data
     *
     * @param Character $character
     * @return array
     */
    public function getLocationData(Character $character): array {
        $this->processLocation($character);

        $this->kingdomManagement($character);

        $lockedLocation = $this->getLockedLocation($character);

        // In case automation is running, this way the timer updates.
        event(new UpdateCharacterStatus($character));

        // Update duel positions.
        event(new UpdateDuelAtPosition($character->user));

        // Update location based crafting options:
        event(new UpdateLocationBasedCraftingOptions($character->user));

        // Update location based special shops:
        event(new UpdateLocationBasedSpecialShops($character->user));

        $this->characterCacheData->removeFromPvpCache($character);

        $this->updateForRankFights($character);

        return [
            'map_url'                => Storage::disk('maps')->url($character->map_url),
            'character_map'          => $character->map,
            'locations'              => $this->fetchLocationData($character),
            'can_move'               => $character->can_move,
            'can_move_again_at'      => $character->can_move_again_at,
            'coordinates'            => $this->coordinatesCache->getFromCache(),
            'celestial_id'           => $this->getCelestialEntityId($character),
            'can_settle_kingdom'     => $this->canSettle,
            'my_kingdoms'            => $this->getKingdoms($character),
            'npc_kingdoms'           => Kingdom::select('id', 'x_position', 'y_position', 'npc_owned', 'name')->whereNull('character_id')->where('game_map_id', $character->map->game_map_id)->where('npc_owned', true)->get(),
            'other_kingdoms'         => $this->getEnemyKingdoms($character),
            'characters_on_map'      => $this->getActiveUsersCountForMap($character),
            'lockedLocationType'     => is_null($lockedLocation) ? null : $lockedLocation->type,
        ];
    }

    protected function updateForRankFights(Character $character): void {
        if (is_null($this->location)) {
            event(new UpdateRankFights($character->user, false));

            return;
        }

        if (is_null($this->location->type)) {
            event(new UpdateRankFights($character->user, false));

            return;
        }

        if ((new LocationType($this->location->type))->isUnderWaterCaves()) {

            event(new UpdateRankFights($character->user, true));

            return;
        }

        event(new UpdateRankFights($character->user, false));
    }

    protected function fetchLocationData(Character $character): Collection {
        $locations = Location::with('questRewardItem')->where('game_map_id', $character->map->game_map_id)->get();

        return $locations->transform(function($location) {

            $location->increases_enemy_stats_by      = null;
            $location->increase_enemy_percentage_by  = null;
            $location->type_name                     = null;

            if (!is_null($location->type)) {
                if ((new LocationType($location->type))->isPurgatorySmithHouse()) {
                    $location->type_name = 'Purgatory Smiths House';
                }

                if ((new LocationType($location->type))->isUnderWaterCaves()) {
                    $location->type_name = 'Underwater Caves';
                }
            }

            if (!is_null($location->enemy_strength_type)) {
                $location->increases_enemy_stats_by     = LocationEffectValue::getIncreaseByAmount($location->enemy_strength_type);
                $location->increase_enemy_percentage_by = LocationEffectValue::fetchPercentageIncrease($location->enemy_strength_type);

                if (!is_null($location->type)) {
                    $locationType = new LocationType($location->type);

                    if ($locationType->isGoldMines()) {
                        $location->type_name = 'Gold Mines';
                    }

                    if ($locationType->isPurgatoryDungeons()) {
                        $location->type_name = 'Purgatory Dungeons';
                    }
                }
            }

            $location->required_quest_item_name = null;

            if (!is_null($location->required_quest_item_id)) {
                $location->required_quest_item_name = $location->requiredQuestItem->name;
            }

            return $location;
        });
    }

    protected function getCelestialEntityId(Character $character) {
        $fight = CelestialFight::with('monster')->join('monsters', function($join) use($character) {
            $join->on('monsters.id', 'celestial_fights.monster_id')
                ->where('x_position', $character->x_position)
                ->where('y_position', $character->y_position)
                ->where('monsters.game_map_id', $character->map->gameMap->id);
        })->select('celestial_fights.id')->first();

        if (!is_null($fight)) {
            return $fight->id;
        }

        return null;
    }

    /**
     * Processes the location.
     *
     * We will fetch the location information for the character position.
     *
     * This includes port details and any relevant adventures the location might have.
     *
     * @param Character $character
     * @return void
     */
    protected function processLocation(Character $character): void {
        $this->location = Location::where('x', $character->x_position)
                                  ->where('y', $character->y_position)
                                  ->where('game_map_id', $character->map->game_map_id)
                                  ->first();
    }

    /**
     * Determines the action the player can take.
     *
     * Based on the character position, if there is a kingdom or not.
     * We determine the action the player can take. That is, can they settle?
     * Can they attack the kingdom or can they manage the kingdom?
     *
     * @param Character $character
     * @return void
     */
    protected function kingdomManagement(Character $character): void {
        if (is_null($this->location)) {
            $this->canSettle = true;
        }
    }

    /**
     * Gets locked location details.
     *
     * @param Character $character
     * @return Location|null
     */
    protected function getLockedLocation(Character $character): ?Location {
        return Location::where('x', $character->map->character_position_x)
                       ->where('y', $character->map->character_position_y)
                       ->where('game_map_id', $character->map->game_map_id)
                       ->whereNotNull('required_quest_item_id')
                       ->first();
    }
}
