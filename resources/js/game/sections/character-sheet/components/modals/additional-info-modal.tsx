import React from "react";
import Dialogue from "../../../../components/ui/dialogue/dialogue";
import {AdditionalInfoModalProps} from "../../../../lib/game/character-sheet/types/modal/additional-info-modal-props";
import Tabs from "../../../../components/ui/tabs/tabs";
import TabPanel from "../../../../components/ui/tabs/tab-panel";
import {formatNumber} from "../../../../lib/game/format-number";

export default class AdditionalInfoModal extends React.Component<AdditionalInfoModalProps, any> {

    private tabs: {key: string, name: string}[];

    constructor(props: AdditionalInfoModalProps) {
        super(props);

        this.tabs = [{
            key: 'stats',
            name: 'Stats'
        }, {
            key: 'holy',
            name: 'Holy',
        }, {
            key: 'voidance',
            name: 'Voidance'
        }, {
            key: 'ambush-counter',
            name: 'Ambush & Counter.'
        }];
    }

    render() {

        if (this.props.character === null) {
            return null;
        }

        return (
            <Dialogue is_open={this.props.is_open}
                      handle_close={this.props.manage_modal}
                      title={this.props.title}
            >
                <Tabs tabs={this.tabs} full_width={true}>
                    <TabPanel key={'stats'}>
                        <div className='grid lg:grid-cols-2 gap-2 overflow-x-auto h-[150px] lg:h-[175px]'>
                            <div>
                                <dl>
                                    <dt>Raw Str</dt>
                                    <dd>{formatNumber(this.props.character.str)}</dd>
                                    <dt>Raw Dex</dt>
                                    <dd>{formatNumber(this.props.character.dex)}</dd>
                                    <dt>Raw Agi</dt>
                                    <dd>{formatNumber(this.props.character.agi)}</dd>
                                    <dt>Raw Chr</dt>
                                    <dd>{formatNumber(this.props.character.chr)}</dd>
                                    <dt>Raw Dur</dt>
                                    <dd>{formatNumber(this.props.character.dur)}</dd>
                                    <dt>Raw Int</dt>
                                    <dd>{formatNumber(this.props.character.int)}</dd>
                                    <dt>Raw Focus</dt>
                                    <dd>{formatNumber(this.props.character.focus)}</dd>
                                </dl>
                            </div>
                            <div className='border-b-2 block lg:hidden border-b-gray-300 dark:border-b-gray-600 my-3'></div>
                            <div>
                                <dl>
                                    <dt>Modded Str</dt>
                                    <dd>{formatNumber(this.props.character.str_modded)}</dd>
                                    <dt>Modded Dex</dt>
                                    <dd>{formatNumber(this.props.character.dex_modded)}</dd>
                                    <dt>Modded Agi</dt>
                                    <dd>{formatNumber(this.props.character.agi_modded)}</dd>
                                    <dt>Modded Chr</dt>
                                    <dd>{formatNumber(this.props.character.chr_modded)}</dd>
                                    <dt>Modded Dur</dt>
                                    <dd>{formatNumber(this.props.character.dur_modded)}</dd>
                                    <dt>Modded Int</dt>
                                    <dd>{formatNumber(this.props.character.int_modded)}</dd>
                                    <dt>Modded Focus</dt>
                                    <dd>{formatNumber(this.props.character.focus_modded)}</dd>
                                </dl>
                            </div>
                        </div>
                        <div className='border-b-2 border-b-gray-300 dark:border-b-gray-600 my-3'></div>
                        <div className='grid lg:grid-cols-2 gap-2'>
                            <div>
                                <dl>
                                    <dt>Weapon Damage</dt>
                                    <dd>{formatNumber(this.props.character.weapon_attack)}</dd>
                                    <dt>Ring Damage</dt>
                                    <dd>{formatNumber(this.props.character.ring_damage)}</dd>
                                    <dt>Spell Damage</dt>
                                    <dd>{formatNumber(this.props.character.spell_damage)}</dd>
                                    <dt>Healing Amount</dt>
                                    <dd>{formatNumber(this.props.character.healing_amount)}</dd>
                                </dl>
                            </div>
                            <div className='border-b-2 block lg:hidden border-b-gray-300 dark:border-b-gray-600 my-3'></div>
                            <div>
                                <dl>
                                    <dt>Voided Weapon Damage <sup>*</sup></dt>
                                    <dd>{formatNumber(this.props.character.voided_weapon_attack)}</dd>
                                    <dt>Voided Ring Damage <sup>*</sup></dt>
                                    <dd>{formatNumber(this.props.character.voided_ring_damage)}</dd>
                                    <dt>Voided Spell Damage <sup>*</sup></dt>
                                    <dd>{formatNumber(this.props.character.voided_spell_damage)}</dd>
                                    <dt>Voided Healing Amount <sup>*</sup></dt>
                                    <dd>{formatNumber(this.props.character.voided_healing_amount)}</dd>
                                </dl>
                            </div>
                        </div>
                        <p className='mt-4 mb-2'>
                            <sup>*</sup> For more information please see <a href='/information/voidance' target='_blank'>Voidance Help <i
                            className="fas fa-external-link-alt"></i></a>
                        </p>
                    </TabPanel>
                    <TabPanel key={'holy'}>
                        <dl>
                            <dt>Holy Bonus:</dt>
                            <dt>{(this.props.character.holy_bonus * 100).toFixed(2)}%</dt>
                            <dt>Holy Stacks:</dt>
                            <dt>{this.props.character.current_stacks} / {this.props.character.max_holy_stacks}</dt>
                            <dt>Holy Attack Bonus:</dt>
                            <dt>{(this.props.character.holy_attack_bonus * 100).toFixed(2)}%</dt>
                            <dt>Holy AC Bonus:</dt>
                            <dt>{(this.props.character.holy_ac_bonus * 100).toFixed(2)}%</dt>
                            <dt>Holy Healing Bonus:</dt>
                            <dt>{(this.props.character.holy_healing_bonus * 100).toFixed(2)}%</dt>
                        </dl>
                        <p className='mt-4'>
                            For more information please see <a href='/information/holy-items' target='_blank'>Holy Items Help <i
                            className="fas fa-external-link-alt"></i></a>
                        </p>
                    </TabPanel>
                    <TabPanel key={'voidance'}>
                        <dl>
                            <dt>Devouring Light:</dt>
                            <dt>{(this.props.character.devouring_light * 100).toFixed(2)}%</dt>
                            <dt>Devouring Light Res.:</dt>
                            <dt>{(this.props.character.devouring_light_res * 100).toFixed(2)}%</dt>
                            <dt>Devouring Darkness:</dt>
                            <dt>{(this.props.character.devouring_darkness * 100).toFixed(2)}%</dt>
                            <dt>Devouring Darkness Res.:</dt>
                            <dt>{(this.props.character.devouring_darkness_res * 100).toFixed(2)}%</dt>
                        </dl>
                        <p className='mt-4'>
                            For more information please see <a href='/information/voidance' target='_blank'>Voidance Help <i
                            className="fas fa-external-link-alt"></i></a>
                        </p>
                    </TabPanel>
                    <TabPanel key={'ambush-counter'}>
                        <dl>
                            <dt>Ambush Chance</dt>
                            <dd>{(this.props.character.ambush_chance * 100).toFixed(2)}%</dd>
                            <dt>Ambush Resistance</dt>
                            <dd>{(this.props.character.ambush_resistance_chance * 100).toFixed(2)}%</dd>
                            <dt>Counter Chance</dt>
                            <dd>{(this.props.character.counter_chance * 100).toFixed(2)}%</dd>
                            <dt>Counter Resistance</dt>
                            <dd>{(this.props.character.counter_resistance_chance * 100).toFixed(2)}%</dd>
                        </dl>
                        <p className='mt-4'>
                            For more information please see <a href='/information/ambush-and-counter' target='_blank'>Ambush and Counter Help <i
                            className="fas fa-external-link-alt"></i></a>
                        </p>
                    </TabPanel>
                </Tabs>
            </Dialogue>
        );
    }
}
