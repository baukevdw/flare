<?php

namespace App\Flare\Services;

use App\Flare\Builders\CharacterBuilder;
use App\Flare\Models\Character;
use App\Flare\Models\GameClass;
use App\Flare\Models\GameMap;
use App\Flare\Models\GameRace;
use App\Flare\Models\Inventory;
use App\Flare\Models\MarketBoard;
use App\Flare\Models\RankFightTop;
use App\Flare\Models\User;
use App\Game\Kingdoms\Handlers\GiveKingdomsToNpcHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CharacterDeletion {

    private GiveKingdomsToNpcHandler $giveKingdomsToNpcHandler;

    private CharacterBuilder $characterBuilder;

    public function __construct(GiveKingdomsToNpcHandler $giveKingdomsToNpcHandler, CharacterBuilder $characterBuilder) {
        $this->giveKingdomsToNpcHandler = $giveKingdomsToNpcHandler;
        $this->characterBuilder         = $characterBuilder;
    }

    public function deleteCharacterFromUser(Character $character, array $params = []) {
        $user = $character->user;

        foreach ($character->kingdoms as $kingdom) {
            $this->giveKingdomsToNpcHandler->giveKingdomToNPC($kingdom);
        }

        if (!is_null($character->inventory)) {
            $this->emptyCharacterInventory($character->inventory);
        }

        if (!$character->inventorySets->isEmpty()) {
            $this->emptyCharacterInventorySets($character->inventorySets);
        }

        if (!$character->mercenaries->isEmpty()) {
            $this->removeMercenaries($character->mercenaries);
        }

        $this->deleteCharacterMarketListings($character);

        $this->deleteCharacter($character);

        if (!empty($params)) {
            $this->createCharacter($user->refresh(), $params);
        }
    }


    protected function createCharacter(User $user, array $params): void {
        $race  = GameRace::find($params['race_id']);
        $class = GameClass::find($params['class_id']);
        $map   = GameMap::where('default', true)->first();

        $this->characterBuilder->setRace($race)
                              ->setClass($class)
                              ->createCharacter($user, $map, $params['name'])
                              ->assignSkills()
                              ->assignPassiveSkills()
                              ->buildCharacterCache();

        $user->refresh()->update([
            'guide_enabled' => $params['guide']
        ]);
    }

    protected function removeMercenaries(Collection $mercenaries): void {
        foreach ($mercenaries as $merc) {
            $merc->delete();
        }
    }

    protected function deleteCharacterMarketListings(Character $character): void {

        MarketBoard::where('character_id', $character->id)->chunkById(250, function($marketListings) {
            foreach ($marketListings as $marketListing) {
                $marketListing->delete();
            }
        });
    }

    protected function emptyCharacterInventory(Inventory $inventory): void {
        foreach ($inventory->slots as $slot) {
            $slot->delete();
        }

        $inventory->delete();
    }

    protected function emptyCharacterInventorySets(Collection $inventorySets): void {
        foreach ($inventorySets as $set) {
            foreach ($set->slots as $slot) {
                $slot->delete();
            }

            $set->delete();
        }
    }

    protected function deleteCharacter(Character $character): void {
        $character->skills()->delete();

        $character->kingdomAttackLogs()->delete();

        $character->unitMovementQueues()->delete();

        $character->boons()->delete();

        $character->questsCompleted()->delete();

        $character->currentAutomations()->delete();

        $character->factions()->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $character->passiveSkills()->delete();

        $character->classRanks->weaponMasteries()->delete();

        $character->classRanks()->delete();

        $character->classSpecialsEquipped()->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        RankFightTop::where('character_id', $character->id)->delete();

        $character->map()->delete();

        $character->delete();
    }
}
