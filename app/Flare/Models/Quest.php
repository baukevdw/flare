<?php

namespace App\Flare\Models;

use App\Flare\Values\FeatureTypes;
use Database\Factories\QuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Flare\Models\Traits\WithSearch;

class Quest extends Model {

    use WithSearch, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'npc_id',
        'item_id',
        'access_to_map_id',
        'gold_dust_cost',
        'shard_cost',
        'gold_cost',
        'copper_coin_cost',
        'reward_item',
        'reward_gold_dust',
        'reward_shards',
        'reward_gold',
        'reward_xp',
        'unlocks_skill',
        'unlocks_skill_type',
        'is_parent',
        'parent_quest_id',
        'secondary_required_item',
        'faction_game_map_id',
        'required_faction_level',
        'before_completion_description',
        'after_completion_description',
        'unlocks_feature',
        'unlocks_passive_id',
    ];

    protected $casts = [
        'name'                    => 'string',
        'item_id'                 => 'integer',
        'gold_dust_cost'          => 'integer',
        'shard_cost'              => 'integer',
        'gold_cost'               => 'integer',
        'copper_coin_cost'        => 'integer',
        'reward_item'             => 'integer',
        'reward_gold_dust'        => 'integer',
        'reward_shards'           => 'integer',
        'reward_gold'             => 'integer',
        'reward_xp'               => 'integer',
        'unlocks_skill'           => 'boolean',
        'is_parent'               => 'boolean',
        'unlocks_skill_type'      => 'integer',
        'parent_quest_id'         => 'integer',
        'faction_game_map_id'     => 'integer',
        'secondary_required_item' => 'integer',
        'required_faction_level'  => 'integer',
        'unlocks_feature'         => 'integer',
        'unlocks_passive_id'      => 'integer'
    ];

    protected $appends = [
        'belongs_to_map_name',
    ];

    public function childQuests() {
        return $this->hasMany($this, 'parent_quest_id')
                    ->with(
                'childQuests'
                    );
    }

    public function loadRelations() {
        return $this->load(
    'rewardItem',
            'item',
            'factionMap',
            'item.dropLocation',
            'secondaryItem',
            'secondaryItem.dropLocation',
            'requiredPlane',
            'npc',
            'npc.gameMap',
        );
    }

    public function parent() {
        return $this->belongsTo($this, 'parent_quest_id');
    }

    public function item() {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function secondaryItem() {
        return $this->belongsTo(Item::class, 'secondary_required_item', 'id');
    }

    public function rewardItem() {
        return $this->belongsTo(Item::class, 'reward_item', 'id');
    }

    public function npc() {
        return $this->belongsTo(Npc::class, 'npc_id', 'id');
    }

    public function requiredPlane() {
        return $this->hasOne(GameMap::class, 'id', 'access_to_map_id');
    }

    public function factionMap() {
        return $this->hasOne(GameMap::class, 'id', 'faction_game_map_id');
    }

    public function unlocksFeature(): ?FeatureTypes {
        if (!is_null($this->unlocks_feature)) {
            return new FeatureTypes($this->unlocks_feature);
        }

        return null;
    }

    public function getBelongsToMapNameAttribute() {
        if (!is_null($this->npc)) {
            return $this->npc->gameMap->name;
        }

        return null;
    }

    public function getRequiredItemMonsterAttribute() {
        if (!is_null($this->item_id)) {
            return Monster::where('quest_item_id', $this->item_id)->with('gameMap')->first();
        }

        return null;
    }

    public function getUnlocksSkillNameAttribute() {
        if ($this->unlocks_skill) {
            return GameSkill::where('type', $this->unlocks_skill_type)->first()->name;
        }

        return null;
    }

    protected static function newFactory() {
        return QuestFactory::new();
    }
}
