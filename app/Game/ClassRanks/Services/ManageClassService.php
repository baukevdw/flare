<?php

namespace App\Game\ClassRanks\Services;

use App\Flare\Handlers\UpdateCharacterAttackTypes;
use App\Flare\Models\Character;
use App\Flare\Models\GameClass;
use App\Flare\Models\GameSkill;
use App\Flare\Values\BaseSkillValue;
use App\Game\Core\Traits\ResponseBuilder;
use App\Game\Skills\Services\UpdateCharacterSkillsService;
use Exception;

class ManageClassService {

    use ResponseBuilder;

    /**
     * @var UpdateCharacterAttackTypes $updateCharacterAttackTypes
     */
    private UpdateCharacterAttackTypes $updateCharacterAttackTypes;

    /**
     * @var UpdateCharacterSkillsService $updateCharacterSkillsService
     */
    private UpdateCharacterSkillsService $updateCharacterSkillsService;

    /**
     * @var ClassRankService $classRankService
     */
    private ClassRankService $classRankService;

    /**
     * @param UpdateCharacterAttackTypes $updateCharacterAttackTypes
     * @param UpdateCharacterSkillsService $updateCharacterSkillsService
     * @param ClassRankService $classRankService
     */
    public function __construct(UpdateCharacterAttackTypes $updateCharacterAttackTypes,
                                UpdateCharacterSkillsService $updateCharacterSkillsService,
                                ClassRankService $classRankService
    ) {
        $this->updateCharacterAttackTypes   = $updateCharacterAttackTypes;
        $this->updateCharacterSkillsService = $updateCharacterSkillsService;
        $this->classRankService             = $classRankService;
    }

    /**
     * Switch character class.
     *
     * - Will hide the current class skill and un hide or add the new class special skill.
     *
     * @param Character $character
     * @param GameClass $class
     * @return array
     * @throws Exception
     */
    public function switchClass(Character $character, GameClass $class): array {

        $gameSkill = GameSkill::where('game_class_id', $character->game_class_id)->first();

        $skillToHide = $character->skills->where('game_skill_id', $gameSkill->id)->first()->id;

        $character->skills()->where('id', $skillToHide)->update(['is_hidden' => true]);

        $character = $character->refresh();

        $skillToAdd = GameSkill::where('game_class_id', $class->id)->first();

        $characterSkill = $character->skills->where('game_skill_id', $skillToAdd->id)->first();

        if (!is_null($characterSkill)) {
            $characterSkill->update(['is_hidden' => false]);
        } else {
            $skillDetails = resolve(BaseSkillValue::class)->getBaseCharacterSkillValue($character, $skillToAdd);

            $character->skills()->create($skillDetails);
        }

        $character = $character->refresh();

        $character->update([
            'game_class_id' => $class->id,
        ]);

        $character = $character->refresh();

        $this->updateCharacterAttackTypes->updateCache($character);

        $this->updateCharacterSkillsService->updateCharacterSkills($character);

        return $this->successResult([
            'message'     => 'You have switched to: ' . $class->name,
            'class_ranks' => $this->classRankService->getClassRanks($character)['class_ranks'],
        ]);
    }
}
