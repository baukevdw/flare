<?php

namespace App\Game\Maps\Events;

use App\Game\Core\Traits\KingdomCache;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
Use App\Flare\Models\User;
use App\Flare\Models\Map;
use App\Game\Maps\Services\MovementService;

class UpdateMapDetailsBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, KingdomCache;

    /**
     * @var Map $map
     */
    public $map;

    /**
     * @var array $portDetails
     */
    public $portDetails;

    /**
     * @var array $adventureDetails
     */
    public $adventureDetails;

    /**
     * @var array $npcKingdoms
     */
    public $npcKingdoms = [];

    /**
     * @var array $kingdomDetails
     */
    public $kingdomDetails = [];

    /**
     * @var array $updatedKingdoms
     */
    public $updatedKingdoms = [];

    /**
     * @var User $user
     */

    private $user;

    /**
     * Create a new event instance.
     *
     * @param Map $map
     * @param User $user
     * @param MovementService $service
     * @param bool $updateKingdoms
     */
    public function __construct(Map $map, User $user, MovementService $service, bool $updateKingdoms = false)
    {
        if ($updateKingdoms) {
            $service->processArea($user->character);

            $this->updatedKingdoms['kingdom_details'] = $this->getKingdoms($user->character);
        }

        $this->map              = $map;
        $this->user             = $user;
        $this->portDetails      = $service->portDetails();
        $this->adventureDetails = $service->adventureDetails();
        $this->kingdomDetails   = $service->kingdomDetails();
        $this->npcKingdoms      = $service->npcOwnedKingdoms();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('update-map-' . $this->user->id);
    }
}
