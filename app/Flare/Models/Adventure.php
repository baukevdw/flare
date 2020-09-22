<?php

namespace App\Flare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\AdventureFactory;

class Adventure extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'reward_item_id',
        'levels',
        'time_per_level',
        'gold_rush_chance',
        'item_find_chance',
        'skill_exp_bonus',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'levels'           => 'integer',
        'time_per_level'   => 'integer',
        'gold_rush_chance' => 'float',
        'item_find_chance' => 'float',
        'skill_exp_bonus'  => 'float',
    ];

    public function monsters() {
        return $this->belongsToMany(Monster::class);
    }

    public function locations() {
        return $this->belongsToMany(Location::class);
    }

    public function itemReward() {
        return $this->hasOne(Item::class, 'id', 'reward_item_id');
    }

    public static function dataTableSearch($query) {
        return empty($query) ? static::query()
            : static::where('name', 'like', '%'.$query.'%');
    }

    protected static function newFactory() {
        return AdventureFactory::new();
    }
}
