
<div class="col-md-4">
    <dl>
        <dt>Strength:</dt>
        <dd>{{$character->str}}</dd>
        <dt>Durability:</dt>
        <dd>{{$character->dur}}</dd>
        <dt>Dexterity:</dt>
        <dd>{{$character->dex}}</dd>
        <dt>Charisma:</dt>
        <dd>{{$character->chr}}</dd>
        <dt>Intelligence:</dt>
        <dd>{{$character->int}}</dd>
        <dt>Agility:</dt>
        <dd>{{$character->agi}}</dd>
        <dt>Focus:</dt>
        <dd>{{$character->focus}}</dd>
    </dl>
</div>
<div class="col-md-4">
    <dl>
        <dt>Strength Modded:</dt>
        <dd>{{$characterInfo['str']}}</dd>
        <dt>Durability Modded:</dt>
        <dd>{{$characterInfo['dur']}}</dd>
        <dt>Dexterity Modded:</dt>
        <dd>{{$characterInfo['dex']}}
        <dt>Charisma Modded:</dt>
        <dd>{{$characterInfo['chr']}}</dd>
        <dt>Intelligence Modded:</dt>
        <dd>{{$characterInfo['int']}}</dd>
        <dt>Agility Modded:</dt>
        <dd>{{$characterInfo['agi']}}</dd>
        <dt>Focus Modded:</dt>
        <dd>{{$characterInfo['focus']}}</dd>
    </dl>
</div>
<div class="col-md-4">
    <dl>
        <dt>Spell Evasion:</dt>
        <dd>{{$character->spell_evasion * 100}}%</dd>
        <dt>Artifact Annulment:</dt>
        <dd>{{$character->artifact_annulment * 100}}%</dd>
    </dl>
</div>
