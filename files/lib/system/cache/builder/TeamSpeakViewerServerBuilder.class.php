<?php

namespace wcf\system\cache\builder;

use wcf\data\teamspeak\Teamspeak;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\WCF;
use wcf\util\TeamSpeakUtil;

class TeamSpeakViewerServerBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = HANASHI_TEAMSPEAK_VIEWER_CACHE_INTERVAL;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $general = [];
        $teamspeakObj = new Teamspeak(HANASHI_TEAMSPEAK_VIEWER_IDENTITY);
        $serverinfo = TeamSpeakViewerHandler::getInstance()->serverinfo();
        if (empty(HANASHI_TEAMSPEAK_VIEWER_ADDRESS)) {
            $serverinfo[0]['hostname'] = $teamspeakObj->hostname;
        } else {
            $serverinfo[0]['hostname'] = HANASHI_TEAMSPEAK_VIEWER_ADDRESS;
        }
        if (empty(HANASHI_TEAMSPEAK_VIEWER_PORT)) {
            $serverinfo[0]['port'] = $teamspeakObj->virtualServerPort;
        } else {
            $serverinfo[0]['port'] = HANASHI_TEAMSPEAK_VIEWER_PORT;
        }
        if ($serverinfo[0]['virtualserver_icon_id'] != 0) {
            $serverinfo[0]['virtualserver_icon_id'] = TeamSpeakUtil::getCorrectIconID(
                $serverinfo[0]['virtualserver_icon_id']
            );
            if (!TeamSpeakViewerHandler::getInstance()->checkIcon($serverinfo[0]['virtualserver_icon_id'])) {
                $serverinfo[0]['virtualserver_icon_id'] = 0;
            }
        }
        return $serverinfo[0];
    }
}
