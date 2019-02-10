<?php
namespace wcf\page;
use wcf\data\teamspeak\Teamspeak;
use wcf\system\cache\builder\TeamSpeakViewerGeneralBuilder;
use wcf\system\exception\TeamSpeakException;
use wcf\system\exception\ErrorException;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\template\TeamSpeakViewerTemplateHandler;
use wcf\system\WCF;

class TeamSpeakViewerPage extends AbstractPage {
    protected $serverinfo;

    protected $channellist;

    protected $clientlist;

    /**
     * @inheritDoc
     */
    public function readData() {
        parent::readData();

        try {
            $data = TeamSpeakViewerGeneralBuilder::getInstance()->getData();
            $this->serverinfo = $data['serverinfo'];
            $this->channellist = $data['channellist'];
            $this->clientlist = $data['clientlist'];
        } catch (TeamSpeakException $e) {
        } catch (ErrorException $e) {}
    }

    /**
     * @inheritDoc
     */
    public function assignVariables() {
        parent::assignVariables();

        // wcfDebug($this->serverinfo);
        // preg_match('/^(\[([a-z\*])[a-zA-Z0-9\*]?spacer([0-9a-zA-Z]+)?\])(.*)$/', '[c0spacer0]Huhu_', $matches);
        // wcfDebug($matches);

        WCF::getTPL()->assign([
            'serverinfo' => $this->serverinfo,
            'channellist' => $this->channellist,
            'clientlist' => $this->clientlist,
            'teamspeakLink' => $this->getTeamspeakLink(),
            'tsTemplate' => new TeamSpeakViewerTemplateHandler()
        ]);
    }

    /**
     * method to generate TeamSpeak link
     * 
     * @return string
     */
    private function getTeamspeakLink() {
        $query = [];
        if($this->serverinfo['port'] != 9987) {
            $query[] = 'port='.$this->serverinfo['port'];
        }
        if ($this->serverinfo['virtualserver_flag_password'] == 1 && HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD && !empty(HANASHI_TEAMSPEAK_VIEWER_PASSWORD)) {
            $query[] = 'password='.HANASHI_TEAMSPEAK_VIEWER_PASSWORD;
        }

        $link = 'ts3server://';
        $link .= urlencode($this->serverinfo['hostname']);
        if (count($query) > 0) {
            $link .= '?'.implode('&', $query);
        }
        return $link;
    }
}