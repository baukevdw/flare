import React, {Fragment} from 'react';
import Card from "../components/templates/card";
import {Tab, Tabs} from "react-bootstrap";
import QuestTree from "./trees/quest-tree";
import AlertInfo from "../components/base/alert-info";

export default class QuestSection extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      loading: true,
      npcs: [],
      firstTab: null,
      completedQuests: [],
    }
  }

  componentDidMount() {
    axios.get('/api/map/quests/' + this.props.characterId).then((result) => {
      this.setState({
        loading: false,
        npcs: result.data.npcs,
        completedQuests: result.data.completed_quests,
        firstTab: result.data.npcs.length > 0 ? result.data.npcs[0].name : null,
      });
    }).catch((err) => {
      if (err.hasOwnProperty('response')) {
        const response = err.response;

        if (response.status === 401) {
          return location.reload();
        }

        if (response.status === 429) {
          return this.props.openTimeOutModal()
        }
      }
    });
  }

  hideQuests() {
    this.props.openQuestDetails(false)
  }

  renderParentQuestTrees(quests) {
    return quests.map((quest) => {
      return <QuestTree parentQuest={quest} />
    });
  }

  renderTabs() {
    return this.state.npcs.map((npc) => {
      return <Tab eventKey={npc.name} title={npc.real_name}>
        <div className="mt-3">
          {this.renderParentQuestTrees(npc.quests.filter((quest) => quest.is_parent))}
        </div>
      </Tab>
    });
  }

  render() {
    return (
      <Card
        OtherCss="p-3"
        cardTitle="Quests"
        close={this.hideQuests.bind(this)}
      >
        {
          this.state.loading ?
            <div className="progress loading-progress mt-2 mb-2" style={{position: 'relative'}}>
              <div className="progress-bar progress-bar-striped indeterminate">
              </div>
            </div>
            :
            this.state.npcs.length === 0 ?
              <Fragment>
                <div className="text-center">There are no quests for this plane.</div>
              </Fragment>
              :
              <Fragment>
                <AlertInfo icon={"fas fa-question-circle"} title={"ATTN!"}>
                  <p>Some quests may require you to speak to a different NPC. All quests are grouped together
                    as parent/child, but some quests belong to a particular NPC.</p>
                  <p>You can click on a quest name to open a modal to get details about that quest. Players
                    can choose to do a specific quest before another, how ever if the quest you want to do has more
                    currency cost then the previous
                    quest in the list (for the same NPC), the previous quest would be done first when handing in
                    currency.</p>
                  <p>If a quest has been completed, you will see a green checkmark beside the quest name. <strong>This
                    tree is not live and will not update
                    in real time if you complete a quest with it open</strong>.</p>
                  <p>
                    If quests have a line separating them, you will want to do them in the order listed.
                    If they do not, then feel free to do them in any order.
                  </p>
                </AlertInfo>
                <Tabs defaultActiveKey={this.state.firstTab} id="map-quests">
                  {this.renderTabs()}
                </Tabs>
              </Fragment>
        }
      </Card>
    );
  }
}
