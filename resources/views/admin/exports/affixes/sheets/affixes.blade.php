<table>
    <thead>
        <tr>
            <th>name</th>
            <th>description</th>
            <th>base_damage_mod</th>
            <th>base_healing_mod</th>
            <th>base_ac_mod</th>
            <th>str_mod</th>
            <th>dur_mod</th>
            <th>dex_mod</th>
            <th>chr_mod</th>
            <th>int_mod</th>
            <th>agi_mod</th>
            <th>focus_mod</th>
            <th>str_reduction</th>
            <th>dur_reduction</th>
            <th>dex_reduction</th>
            <th>chr_reduction</th>
            <th>int_reduction</th>
            <th>agi_reduction</th>
            <th>focus_reduction</th>
            <th>reduces_enemy_stats</th>
            <th>steal_life_amount</th>
            <th>class_bonus</th>
            <th>damage</th>
            <th>int_required</th>
            <th>skill_level_required</th>
            <th>skill_level_trivial</th>
            <th>skill_name</th>
            <th>affects_skill_type</th>
            <th>skill_bonus</th>
            <th>skill_training_bonus</th>
            <td>base_damage_mod_bonus</td>
            <td>base_healing_mod_bonus</td>
            <td>base_ac_mod_bonus</td>
            <td>fight_time_out_mod_bonus</td>
            <td>move_time_out_mod_bonus</td>
            <th>can_drop</th>
            <th>damage_can_stack</th>
            <th>irresistible_damage</th>
            <th>cost</th>
            <th>type</th>
            <th>entranced_chance</th>
            <th>devouring_light</th>
        </tr>
    </thead>
    <tbody>
    @foreach($affixes as $affix)
        <tr>
            <td>{{$affix->name}}</td>
            <td>{{$affix->description}}</td>
            <td>{{$affix->base_damage_mod}}</td>
            <td>{{$affix->base_healing_mod}}</td>
            <td>{{$affix->base_ac_mod}}</td>
            <td>{{$affix->str_mod}}</td>
            <td>{{$affix->dur_mod}}</td>
            <td>{{$affix->dex_mod}}</td>
            <td>{{$affix->chr_mod}}</td>
            <td>{{$affix->int_mod}}</td>
            <td>{{$affix->agi_mod}}</td>
            <td>{{$affix->focus_mod}}</td>
            <td>{{$affix->str_reduction}}</td>
            <td>{{$affix->dur_reduction}}</td>
            <td>{{$affix->dex_reduction}}</td>
            <td>{{$affix->chr_reduction}}</td>
            <td>{{$affix->int_reduction}}</td>
            <td>{{$affix->agi_reduction}}</td>
            <td>{{$affix->focus_reduction}}</td>
            <td>{{$affix->reduces_enemy_stats}}</td>
            <td>{{$affix->steal_life_amount}}</td>
            <td>{{$affix->class_bonus}}</td>
            <td>{{$affix->damage}}</td>
            <td>{{$affix->int_required}}</td>
            <td>{{$affix->skill_level_required}}</td>
            <td>{{$affix->skill_level_trivial}}</td>
            <td>{{$affix->skill_name}}</td>
            <td>{{$affix->affects_skill_type}}</td>
            <td>{{$affix->skill_bonus}}</td>
            <td>{{$affix->skill_training_bonus}}</td>
            <td>{{$affix->base_damage_mod_bonus}}</td>
            <td>{{$affix->base_healing_mod_bonus}}</td>
            <td>{{$affix->base_ac_mod_bonus}}</td>
            <td>{{$affix->fight_time_out_mod_bonus}}</td>
            <td>{{$affix->move_time_out_mod_bonus}}</td>
            <td>{{$affix->can_drop}}</td>
            <td>{{$affix->damage_can_stack}}</td>
            <td>{{$affix->irresistible_damage}}</td>
            <td>{{$affix->cost}}</td>
            <td>{{$affix->type}}</td>
            <td>{{$affix->entranced_chance}}</td>
            <td>{{$affix->devouring_light}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
