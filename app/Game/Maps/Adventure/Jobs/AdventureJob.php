<?php

namespace App\Game\Maps\Adventure\Jobs;

use App\Flare\Models\Adventure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Flare\Models\Character;
use App\Game\Maps\Adventure\Builders\RewardBuilder;
use App\Game\Maps\Adventure\Services\AdventureService;
use Cache;

class AdventureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $character;

    protected $adventure;

    protected $name;

    protected $repeatingAdventure;

    protected $currentLevel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Character $character, Adventure $adventure, String $name, int $currentLevel)
    {
        $this->character          = $character;
        $this->adventure          = $adventure;
        $this->name               = $name;
        $this->currentLevel       = $currentLevel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RewardBuilder $rewardBuilder)
    {
        $name = Cache::get('character_'.$this->character->id.'_adventure_'.$this->adventure->id);

        if (is_null($name) || $name !== $this->name) {
            return;
        }

        $adevntureService = resolve(AdventureService::class, [
            'character'           => $this->character->refresh(),
            'adventure'           => $this->adventure,
            'rewardBuilder'       => $rewardBuilder,
            'name'                => $this->name
        ]);

        $adevntureService->processAdventure($this->currentLevel, $this->adventure->levels);

        if ($this->currentLevel === $this->adventure->levels) {
            Cache::forget('character_'.$this->character->id.'_adventure_'.$this->adventure->id);
        }
    }
}
