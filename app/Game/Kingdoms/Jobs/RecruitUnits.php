<?php

namespace App\Game\Kingdoms\Jobs;


use App\Game\Kingdoms\Service\UpdateKingdom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Flare\Events\ServerMessageEvent;
use App\Flare\Models\GameUnit;
use App\Flare\Models\Kingdom;
use App\Flare\Models\UnitInQueue;
use Facades\App\Flare\Values\UserOnlineValue;

class RecruitUnits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Kingdom $kingdom
     */
    protected $kingdom;

    /**
     * @var GameUnit $unit
     */
    protected $unit;

    /**
     * @var int queueId
     */
    protected $queueId;

    /**
     * @var int $amount
     */
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @param GameUnit $unit
     * @param Kingdom $kingdom
     * @param int $amount
     * @param int $queueId
     * @return void
     */
    public function __construct(GameUnit $unit, Kingdom $kingdom, int $amount, int $queueId)
    {
        $this->kingdom  = $kingdom;

        $this->unit     = $unit;

        $this->queueId  = $queueId;

        $this->amount   = $amount;
    }

    /**
     * Execute the job.
     *
     * @param UpdateKingdom $updateKingdom
     * @return void
     */
    public function handle(UpdateKingdom $updateKingdom)
    {

        $queue = UnitInQueue::find($this->queueId);

        if (is_null($queue)) {
            return;
        }

        if (!$queue->completed_at->lessThanOrEqualTo(now())) {
            $timeLeft = $queue->completed_at->diffInMinutes(now());

            if ($timeLeft <= 15) {
                $time = now()->addMinutes($timeLeft);
            } else {
                $time = now()->addMinutes(15);
            }

            // @codeCoverageIgnoreStart
            RecruitUnits::dispatch(
                $this->unit,
                $this->kingdom,
                $this->amount,
                $this->queueId,
            )->delay($time);

            return;
            // @codeCoverageIgnoreEnd
        }

        $amount = $this->amount;

        if ($this->kingdom->units->isEmpty()) {
            $this->kingdom->units()->create([
                'kingdom_id'   => $this->kingdom->id,
                'game_unit_id' => $this->unit->id,
                'amount'       => $amount,
            ]);
        } else {
            $found = $this->kingdom->units()->where('game_unit_id', $this->unit->id)->first();

            if (is_null($found)) {
                $this->kingdom->units()->create([
                    'kingdom_id'   => $this->kingdom->id,
                    'game_unit_id' => $this->unit->id,
                    'amount'       => $amount,
                ]);
            } else {
                $amount += $found->amount;

                $found->update([
                    'amount' => $amount,
                ]);
            }
        }

        $queue->delete();

        $kingdom = $this->kingdom->refresh();

        $updateKingdom->updateKingdom($kingdom);

        $x       = $kingdom->x_position;
        $y       = $kingdom->y_position;
        $user    = $kingdom->character->user;
        $plane   = $kingdom->gameMap->name;

        if (UserOnlineValue::isOnline($user)) {

            if ($user->show_unit_recruitment_messages) {
                $message = $this->unit->name . ' finished recruiting for kingdom: ' .
                    $this->kingdom->name . ' on plane: ' . $plane . ' at: (X/Y) ' . $x . '/' . $y .
                    '. You have a total of: ' . number_format($amount);


                event(new ServerMessageEvent($user, 'unit-recruitment-finished', $message));
            }
        }
    }
}
