<?php

namespace App\Game\Quests\Controllers\Api;

use App\Flare\Models\PassiveSkill;
use App\Game\Messages\Events\GlobalMessageEvent;
use App\Game\Quests\Services\QuestHandlerService;
use App\Game\Skills\Values\SkillTypeValue;
use Cache;
use App\Flare\Models\Character;
use App\Flare\Models\Quest;
use App\Http\Controllers\Controller;

class QuestsController extends Controller {

    private $questHandler;

    public function __construct(QuestHandlerService $questHandlerService) {
        $this->questHandler = $questHandlerService;
    }

    public function index(Character $character) {

        $quests = Quest::where('is_parent', true)->with('childQuests')->get();

        return response()->json([
            'completed_quests' => $character->questsCompleted()->pluck('quest_id'),
            'quests'           => $quests->toArray(),
            'player_plane'     => $character->map->gameMap->name,
        ]);
    }

    public function quest(Quest $quest, Character $character) {
        $quest = $quest->loadRelations();

        if ($quest->unlocks_skill) {
            $quest->unlocks_skill_name = (new SkillTypeValue($quest->unlocks_skill_type))->getNamedValue();
        }

        if (!$quest->unlocks_skill) {
            $quest->unlocks_skill_name = 'N/A';
        }

        if (!is_null($quest->unlocks_feature)) {
            $quest->feature_to_unlock_name = $quest->unlocksFeature()->getNameOfFeature();
        } else {
            $quest->feature_to_unlock_name = null;
        }

        if (!is_null($quest->unlocks_passive_id)) {
            $quest->unlocks_passive_name = PassiveSkill::find($quest->unlocks_passive_id)->name;
        } else {
            $quest->unlocks_passive_name = null;
        }

        return response()->json($quest);
    }

    public function handInQuest(Quest $quest, Character $character) {
        if ($this->questHandler->shouldBailOnQuest($character, $quest)) {
            return response()->json([
                'message' => $this->questHandler->getBailMessage()
            ], 422);
        }

        $characterIsAtLocation = $character->map()
                                           ->where('x_position', $quest->npc->x_position)
                                           ->where('y_position', $quest->npc->y_position)
                                           ->where('game_map_id', $quest->npc->game_map_id);

        if (!is_null($characterIsAtLocation)) {

            $response = $this->questHandler->moveCharacter($character, $quest->npc);

            if ($response instanceof Character) {
                $response = $this->questHandler->handInQuest($character, $quest);
            }
        } else {
            $response = $this->questHandler->handInQuest($character, $quest);
        }

        if ($response['status'] === 422) {
            unset($response['status']);

            return response()->json($response, 422);
        }

        unset($response['status']);

        $response['message'] = 'You completed the quest: ' . $quest->name . '. Above is the updated story for the quest.';

        return response()->json($response);


    }
}
