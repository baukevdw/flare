import React from 'react';

export default class CharacterDestroyWarning extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      itemToDestroy: this.props.itemToDestroy,
    }
  }

  renderAffixes(item) {
    return item.item_affixes.map((affix) => {
      return (
        <div key ={affix.id}>
          <dl>
            <dt>Name:</dt>
            <dd>{affix.name}</dd>
          </dl>
          <dl>
            <dt>Base Damage Mod:</dt>
            <dd>{'+' + affix.base_damage_mod}</dd>
          </dl>
          <div className="mt-2 mb-2 text-center"><i>{affix.description}</i></div>
        </div>
      )
    })
  }

  renderButtons(item) {
    switch(item.type) {
      case 'weapon':
        return (
          <>
            <button className="btn btn-primary" onClick={this.equip.bind(this)} data-type="left-hand">Left Hand</button>
            <button className="btn btn-primary ml-2" onClick={this.equip.bind(this)} data-type="right-hand">Right Hand</button>
          </>
        );
      default:
        return <button className="btn btn-primary" onClick={this.equip.bind(this)} data-type={item.type}>Confirm</button>
    }
  }

  render() {
    const item = this.state.itemToDestroy;

    return (
      <>
        <div className="row">
          <div className="col-md-12">
            <div className="alert alert-warning mb-2">
              This will destroy the item from your inventory. Are you sure?
            </div>
            <div className="card">
              <div className="card-header">
                {item.name}
              </div>
              <div className="card-body">
                <dl>
                  <dt>Base Damage:</dt>
                  <dd>{item.base_damage}</dd>
                </dl>
                <dl>
                  <dt>Type:</dt>
                  <dd>{item.type}</dd>
                </dl>
                <hr />
                {item.artifact_property !== null
                 ?
                  <>
                   <h5>Artifact Details</h5>
                   <dl>
                     <dt>Name:</dt>
                     <dd>{item.artifact_property.name}</dd>
                   </dl>
                   <dl>
                     <dt>Base Damage Mod:</dt>
                     <dd>{'+' + item.artifact_property.base_damage_mod}</dd>
                   </dl>
                   <div className="mt-2 mb-2 text-center"><i>{item.artifact_property.description}</i></div>
                   <hr />
                  </>
                 : null
                }
                {item.item_affixes.length > 0
                 ?
                  <>
                   <h5>Item Affixes</h5>
                   {this.renderAffixes(item)}
                   <hr />
                  </>
                 : null
                }
              </div>
            </div>
          </div>
        </div>
      </>
    );
  }
}
