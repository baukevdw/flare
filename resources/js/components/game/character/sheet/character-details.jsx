import React, {Fragment} from 'react';
import {Card, Col, Row, Tabs, Tab} from 'react-bootstrap';

export default class CharacterDetails extends React.Component {

  constructor(props) {
    super(props);
  }

  buildEachTab(attackData, voided) {
    const tabs = [];

    const attackDataKeys = Object.keys(attackData)
      .filter(key => voided ? !key.includes('voided') : key.includes('voided'));

    for (const key in attackData) {
      if (attackDataKeys.includes(key)) {
        tabs.push(
          <Tab eventKey={key}
               title={key.replace(/_/g, " ").replace(/(^\w{1})|(\s{1}\w{1})/g, match => match.toUpperCase())}
               tabClassName="mt-4"
          >
            <div className="mt-4">
              <Row>
                <Col xs={12} sm={12} md={12} lg={6}>
                  <h4>Attack Data</h4>
                  <hr />
                  <dl>
                    {this.renderAttackData(attackData[key])}
                  </dl>
                </Col>
                <Col xs={12} sm={12} md={12} lg={6}>
                  <h4>Affix Attack Data</h4>
                  <hr />
                  <dl>
                    {this.renderAttackData(attackData[key].affixes)}
                  </dl>
                </Col>
              </Row>
            </div>
          </Tab>
        );
      }
    }

    return tabs;
  }

  renderAttackData(attackData) {

    const data = [];

    for (const key in attackData) {
      if (key !== 'affixes' && key !== 'name') {
        data.push(
          <Fragment>
            <dt>{key.replace(/_/g, " ").replace(/(^\w{1})|(\s{1}\w{1})/g, match => match.toUpperCase())}</dt>
            <dd>{typeof attackData[key] === 'boolean' ? (attackData[key] ? 'Yes' : 'No') : this.formatNumber(attackData[key])}</dd>
          </Fragment>
        )
      }
    }

    return data;
  }

  formatNumber(number) {
    return parseFloat(number).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  render() {

    const sheet = this.props.characterSheet;

    const xpValue = sheet.xp / sheet.xp_next * 100;

    return (
      <Card>
        <Card.Body>
          <Row>
            <Col xs={12} sm={6}>
              <dl>
                <dt>Character Name:</dt>
                <dd>{sheet.name}</dd>
                <dt>Character Race:</dt>
                <dd>{sheet.race}</dd>
                <dt>Character Class:</dt>
                <dd>{sheet.class}</dd>
                <dt>Character Level:</dt>
                <dd>{sheet.level} / {sheet.max_level}</dd>
                <dt>Character XP:</dt>
                <dd>
                  <div className="progress level-bar mb-2">
                    <div className="progress-bar skill-bar" role="progressbar"
                         style={{width: xpValue + '%'}}
                         aria-valuenow={sheet.xp} aria-valuemin="0"
                         aria-valuemax={sheet.xp_next}
                    >
                      {Math.round(sheet.xp)}
                    </div>
                  </div>
                </dd>
              </dl>
            </Col>
            <Col xs={12} sm={6}>
              <dl>
                <dt>Max Health:</dt>

                <dd>{sheet.health}</dd>
                <dt>Attack:</dt>
                <dd>{sheet.attack}</dd>
                <dt>Heal For:</dt>
                <dd>{sheet.heal_for}</dd>
                <dt>AC:</dt>
                <dd>{sheet.ac}</dd>
              </dl>
            </Col>
          </Row>
          <hr />
          <Tabs defaultActiveKey="stats" id="character-stats">
            <Tab eventKey="stats" title="Stats">
              <dl className="mt-4">
                <dt>Strength:</dt>
                <dd>{sheet.str}</dd>
                <dt>Durability:</dt>
                <dd>{sheet.dur}</dd>
                <dt>Dexterity:</dt>
                <dd>{sheet.dex}</dd>
                <dt>Charisma:</dt>
                <dd>{sheet.chr}</dd>
                <dt>Intelligence:</dt>
                <dd>{sheet.int}</dd>
                <dt>Agility:</dt>
                <dd>{sheet.agi}</dd>
                <dt>Focus:</dt>
                <dd>{sheet.focus}</dd>
              </dl>
            </Tab>
            <Tab eventKey="stats-modded" title="Stats Modded">
              <dl className="mt-4">
                <dt>Strength Modded:</dt>
                <dd>{sheet.str_modded}</dd>
                <dt>Durability Modded:</dt>
                <dd>{sheet.dur_modded}</dd>
                <dt>Dexterity Modded:</dt>
                <dd>{sheet.dex_modded}</dd>
                <dt>Charisma Modded:</dt>
                <dd>{sheet.chr_modded}</dd>
                <dt>Intelligence Modded:</dt>
                <dd>{sheet.int_modded}</dd>
                <dt>Agility Modded:</dt>
                <dd>{sheet.agi_modded}</dd>
                <dt>Focus Modded:</dt>
                <dd>{sheet.docus_modded}</dd>
              </dl>
            </Tab>
            <Tab eventKey="resistances-and-reductions" title="Resistances and Deductions">
              <dl className="mt-4">
                <dt>Spell Evasion:</dt>
                <dd>{(sheet.spell_evasion * 100).toFixed(2)}%</dd>
                <dt>Artifact Annulment:</dt>
                <dd>{(sheet.artifact_anull * 100).toFixed(2)}%</dd>
                <dt>Enchantment Reduction Amount<sup>**</sup>:</dt>
                <dd>{(sheet.affix_damage_red * 100).toFixed(2)}%</dd>
                <dt>Healing Reduction Amount<sup>**</sup>:</dt>
                <dd>{(sheet.affix_damage_red * 100).toFixed(2)}%</dd>
                <dt>Resurrection Chance<sup>*</sup>:</dt>
                <dd>{(sheet.res_chance * 100).toFixed(2)}%</dd>
              </dl>
              <p className="mt-4"><sup>*</sup> Only healing spells can affect this.</p>
              <p className="mt-4"><sup>**</sup> Only affects enemies (on their turn).</p>
            </Tab>
            <Tab eventKey="voidance" title="Devouring Light/Darkness">
              <dl className="mt-4">
                <dt>Devouring Light:</dt>
                <dd>{(sheet.devouring_light * 100).toFixed(0)}%</dd>
                <dt>Devouring Darkness:</dt>
                <dd>{(sheet.devouring_darkness * 100).toFixed(0)}%</dd>
              </dl>
              <p className="mt-4">For more information, please see <a href="/information/voidance">Voidance help</a>. </p>
            </Tab>
          </Tabs>
          <hr />
          <Row>
            <Col xs={12}>
              <h5>Attack Break Down</h5>
              <p className="mt-2">
                These include any attached affixes and skill bonuses:
              </p>
              <hr/>
              <Tabs defaultActiveKey="class-bonus" id="character-attack-info">
                <Tab eventKey="class-bonus" title="Class Bonus">
                  <p className="mt-4">
                    {sheet.class_bonus.description}
                  </p>

                  <dl className="mt-2">
                    <dt>Type:</dt>
                    <dd>{sheet.class_bonus.type}</dd>
                    <dt>Base Chance:</dt>
                    <dd>{(sheet.class_bonus.base_chance * 100).toFixed(2)}%</dd>
                    <dt>Requirements:</dt>
                    <dd>{sheet.class_bonus.requires}</dd>
                  </dl>
                </Tab>
                <Tab eventKey="basic-attack" title="Basic Attack Info">
                  <dl className="mt-4">
                    <dt>Weapon Attack:</dt>
                    <dd>{sheet.weapon_attack}</dd>
                    <dt>Rings Attack:</dt>
                    <dd>{sheet.rings_attack}</dd>
                    <dt>Spell Damage:</dt>
                    <dd>{sheet.spell_damage}</dd>
                    <dt>Artifact Damage:</dt>
                    <dd>{sheet.artifact_damage}</dd>
                    <dt>Heal For:</dt>
                    <dd>{sheet.heal_for}</dd>
                  </dl>
                </Tab>
                <Tab eventKey="attack-break-down" title="Attack Break Down">
                  <Tabs defaultActiveKey="regular-attack" id="character-attack-break-down">
                    <Tab eventKey="regular-attack" title="Regular Attacks" tabClassName="mt-4">
                      <Tabs defaultActiveKey="attack" id="character-regular-attack-break-down">
                        {this.buildEachTab(sheet.attack_stats, true)}
                      </Tabs>
                    </Tab>
                    <Tab eventKey="voided-attack" title="Voided Attacks" tabClassName="mt-4">
                      <Tabs defaultActiveKey="voided_attack" id="character-voided-attack-break-down">
                        {this.buildEachTab(sheet.attack_stats, false)}
                      </Tabs>
                    </Tab>
                  </Tabs>
                </Tab>
              </Tabs>
            </Col>
          </Row>
        </Card.Body>
      </Card>
    )
  }
}