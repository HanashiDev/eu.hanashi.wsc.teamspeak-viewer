<?php
namespace wcf\page;
use wcf\data\teamspeak\Teamspeak;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\template\TeamSpeakViewerTemplateHandler;
use wcf\system\WCF;

class TeamSpeakViewerPage extends AbstractPage {
    protected $serverinfo;

    protected $channellist;

    protected $clientlist;

    protected $teamspeakObj;

    /**
     * @inheritDoc
     */
    public function readData() {
        parent::readData();

        $this->serverinfo = TeamSpeakViewerHandler::getInstance()->serverinfo();
        $this->channellist = TeamSpeakViewerHandler::getInstance()->getChannels();
        $this->clientlist = TeamSpeakViewerHandler::getInstance()->getClients();
        $this->teamspeakObj = new Teamspeak(HANASHI_TEAMSPEAK_VIEWER_IDENTITY);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables() {
        parent::assignVariables();

        // wcfDebug($this->clientlist);
        // preg_match('/^(\[([a-z\*])[a-zA-Z0-9\*]?spacer([0-9a-zA-Z]+)?\])(.*)$/', '[c0spacer0]Huhu_', $matches);
        // wcfDebug($matches);

        WCF::getTPL()->assign([
            'serverinfo' => $this->serverinfo,
            'channellist' => $this->channellist,
            'clientlist' => $this->clientlist,
            'teamspeakObj' => $this->teamspeakObj,
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
        if (empty(HANASHI_TEAMSPEAK_VIEWER_PORT)) {
            if ($this->teamspeakObj->virtualServerPort != 9987) {
                $query[] = 'port='.$this->teamspeakObj->virtualServerPort;
            }
        } else if(HANASHI_TEAMSPEAK_VIEWER_PORT != 9987) {
            $query[] = 'port='.HANASHI_TEAMSPEAK_VIEWER_PORT;
        }
        if ($this->serverinfo[0]['virtualserver_flag_password'] == 1 && HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD && !empty(HANASHI_TEAMSPEAK_VIEWER_PASSWORD)) {
            $query[] = 'password='.HANASHI_TEAMSPEAK_VIEWER_PASSWORD;
        }

        $link = 'ts3server://';
        if (empty(HANASHI_TEAMSPEAK_VIEWER_ADDRESS)) {
            $link .= urlencode($this->teamspeakObj->hostname);
        } else {
            $link .= urlencode(HANASHI_TEAMSPEAK_VIEWER_ADDRESS);
        }
        if (count($query) > 0) {
            $link .= '?'.implode('&', $query);
        }
        return $link;
    }
}