<table>
    <thead>
    <tr>
        <th>name</th>
        <th>npc_id</th>
        <th>item_id</th>
        <th>gold_dust_cost</th>
        <th>shard_cost</th>
        <th>gold_cost</th>
        <th>reward_item</th>
        <th>reward_gold_dust</th>
        <th>reward_shards</th>
        <th>reward_gold</th>
        <th>reward_xp</th>
        <th>unlocks_skill</th>
        <th>unlocks_skill_type</th>
    </tr>
    </thead>
    <tbody>
    @foreach($quests as $quest)
        <tr>
            <td>{{$quest->name}}</td>
            <td>{{$quest->npc->real_name}}</td>
            <td>{{!is_null($quest->item) ? $quest->item->name : ''}}</td>
            <td>{{$quest->gold_dust_cost}}</td>
            <td>{{$quest->shard_cost}}</td>
            <td>{{$quest->gold_cost}}</td>
            <td>{{!is_null($quest->rewardItem) ? $quest->rewardItem->name : ''}}</td>
            <td>{{$quest->reward_gold_dust}}</td>
            <td>{{$quest->reward_shards}}</td>
            <td>{{$quest->reward_gold}}</td>
            <td>{{$quest->reward_xp}}</td>
            <td>{{$quest->unlocks_skill}}</td>
            <td>{{$quest->unlocks_skill_type}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
