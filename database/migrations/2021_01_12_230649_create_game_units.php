<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description');
            $table->intger('attack');
            $table->integer('deffense');
            $table->boolean('can_heal')->nullable()->default(false);
            $table->integer('unit_can_heal')->nullable();
            $table->boolean('siege_weapon')->nullable()->default(false);
            $table->integer('travel_time');
            $table->integer('wood_cost');
            $table->integer('clay_cost');
            $table->integer('stone_cost');
            $table->integer('iron_cost');
            $table->integer('required_population');
            $table->intger('time_to_recruit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_units');
    }
}
