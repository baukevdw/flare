<?php

namespace App\Console\Commands;

use App\Admin\Services\ItemAffixService;
use App\Flare\Events\ServerMessageEvent;
use App\Game\Core\Events\UpdateTopBarEvent;
use App\Flare\Models\Character;
use App\Flare\Models\InventorySlot;
use App\Flare\Models\Item;
use App\Flare\Models\ItemAffix;
use App\Flare\Models\MarketBoard;
use App\Flare\Models\MarketHistory;
use App\Flare\Models\Skill;
use App\Game\Battle\Values\MaxLevel;
use App\Game\Core\Services\CharacterService;
use Facades\App\Flare\Calculators\SellItemCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LevelCharacter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'level:character {id} {levels}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Level a character';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CharacterService $characterService)
    {
        $character = Character::find($this->argument('id'));

        if (is_null($character)) {
            return $this->error('Character not found.');
        }

        $bar = $this->output->createProgressBar($this->argument('levels'));

        $bar->start();

        for ($i = 1; $i <= $this->argument('levels'); $i++) {
            $characterService->levelUpCharacter($character, 0);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(1);

        $this->line('All Done! Character leveled to: ' . $this->argument('levels') + 1);
    }
}
