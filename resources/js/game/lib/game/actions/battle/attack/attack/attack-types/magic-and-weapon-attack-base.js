import WeaponAttack from "./weapon-attack";
import UseItems from "./use-items";
import CastAttack from "./cast-attack";
import BattleBase from "../../../battle-base";


export default class MagicAndWeaponAttackBase extends BattleBase {

  constructor(attacker, defender, characterCurrentHealth, monsterHealth, voided) {

    super();

    if (!defender.hasOwnProperty('name')) {
      this.defender = defender.monster;
    } else {
      this.defender = defender;
    }

    this.attacker               = attacker;
    this.monsterHealth          = monsterHealth;
    this.characterCurrentHealth = characterCurrentHealth;
    this.voided                 = voided;
  }

  setStateInfo(attackClass) {
    const state = attackClass.setState();

    this.monsterHealth          = state.monsterCurrentHealth;
    this.characterCurrentHealth = state.characterCurrentHealth;

    this.mergeMessages(state.battle_messages)

    return state;
  }

  setState() {

    const state = {
      characterCurrentHealth: this.characterCurrentHealth,
      monsterCurrentHealth: this.monsterHealth,
      battle_messages: this.getMessages(),
    }

    return state;
  }

  autoCastAndAttack(attackData, castAttack, canHitCheck, canEntrance) {
    this.mergeMessages(canHitCheck.getBattleMessages());

    if (attackData.spell_damage > 0) {
      castAttack.attackWithSpells(attackData, false, true);
    }

    if (attackData.heal_for > 0) {
      castAttack.healWithSpells(attackData);
    }

    this.setStateInfo(castAttack);

    const weaponAttack     = new WeaponAttack(this.attacker, this.defender, this.characterCurrentHealth, this.monsterHealth, this.voided);

    weaponAttack.attackWithWeapon(attackData, false, true, false);

    this.setStateInfo(weaponAttack);

    this.useItems(attackData, this.attacker.class);

    return this.setState();
  }

  autoAttackAndCast(attackData, canHitCheck, canEntrance) {
    this.mergeMessages(canHitCheck.getBattleMessages());

    return this.doWeaponCastAttack(attackData, canEntrance);
  }

  entrancedCastThenAttack(attackData, castAttack, canEntranceEnemy, canEntrance) {
    this.battle_messages.push(...canEntranceEnemy.getBattleMessages())

    if (attackData.spell_damage > 0) {
      castAttack.attackWithSpells(attackData, true, true);
    }

    if (attackData.heal_for > 0) {
      castAttack.healWithSpells(attackData);
    }

    this.setStateInfo(castAttack);

    const weaponAttack     = new WeaponAttack(this.attacker, this.defender, this.characterCurrentHealth, this.monsterHealth, this.voided);

    weaponAttack.attackWithWeapon(attackData, true, false, false);

    this.setStateInfo(weaponAttack);


    this.useItems(attackData, this.attacker.class);

    return this.setState();
  }


  entrancedWeaponThenCastAttack(attackData, canEntranceEnemy, canEntrance) {
    this.mergeMessages(canEntranceEnemy.getBattleMessages());

    return this.doWeaponCastAttack(attackData, canEntrance);
  }

  doWeaponCastAttack(attackData, canEntrance) {
    const weaponAttack     = new WeaponAttack(this.attacker, this.defender, this.characterCurrentHealth, this.monsterHealth, this.voided);

    weaponAttack.attackWithWeapon(attackData, canEntrance, false, true);

    this.setStateInfo(weaponAttack);

    const castAttack       = new CastAttack(this.attacker, this.defender, this.characterCurrentHealth, this.monsterHealth, this.voided);

    if (attackData.spell_damage > 0) {
      castAttack.attackWithSpells(attackData, canEntrance, false);
    }

    if (attackData.heal_for > 0) {
      castAttack.healWithSpells(attackData);
    }

    this.setStateInfo(castAttack);

    this.useItems(attackData, this.attacker.class);

    return this.setState();
  }

  castAttack(attackData, castAttack, canHitCheck, canCast, canUseSpecial) {
    const spellDamage = attackData.spell_damage;

    if (spellDamage > 0) {

      if (canCast) {
        if (this.canBlock(attackData.spell_damage)) {

          this.addMessage(this.defender.name + ' blocked your damaging spell!', 'enemy-action');

          if (attackData.heal_for > 0) {
            castAttack.healWithSpells(attackData);
          }
        } else {
          if (attackData.spell_damage > 0) {
            castAttack.attackWithSpells(attackData, false, canUseSpecial);
          } else if (attackData.heal_for > 0) {
            castAttack.healWithSpells(attackData);
          }
        }
      } else {

        this.addMessage('Your damage spell missed!', 'enemy-action');
      }

      this.battle_messages    = [...this.battle_messages, canHitCheck.getBattleMessages()]
    } else {
      castAttack.healWithSpells(attackData);
    }
  }

  weaponAttack(attackData, weaponAttack, canHitCheck, canHit, canUseSpecial) {
    if (canHit) {
      if (this.canBlock(attackData.weapon_damage)) {
        this.addMessage('Your weapon was blocked!', 'enemy-action')
      } else {
        weaponAttack.attackWithWeapon(attackData, false, false, canUseSpecial);

      }
    } else {
      this.addMessage('Your weapon missed!', 'enemy-action');
    }

    this.mergeMessages(canHitCheck.getBattleMessages());
  }


  useItems(attackData, attackerClass) {
    const useItems = new UseItems(this.defender, this.monsterHealth, this.characterCurrentHealth);

    useItems.useItems(attackData, attackerClass, this.voided);

    this.monsterHealth          = useItems.getMonsterCurrentHealth();
    this.characterCurrentHealth = useItems.getCharacterCurrentHealth();

    this.mergeMessages(useItems.getBattleMessage());
  }

  canBlock(damage) {
    return this.defender.ac > damage;
  }
}
