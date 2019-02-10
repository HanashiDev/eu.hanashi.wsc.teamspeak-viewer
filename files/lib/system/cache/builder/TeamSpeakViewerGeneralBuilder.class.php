<?php
namespace wcf\system\cache\builder;
use wcf\data\teamspeak\Teamspeak;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\WCF;

class TeamSpeakViewerGeneralBuilder extends AbstractCacheBuilder {
    /**
     * @inheritDoc
     */
    protected $maxLifetime = HANASHI_TEAMSPEAK_VIEWER_CACHE_INTERVAL;
    
    /**
	 * @inheritDoc
	 */
    protected function rebuild(array $parameters) {
        $general = [];
        $general['serverinfo'] = TeamSpeakViewerServerBuilder::getInstance()->getData();
        $general['channellist'] = TeamSpeakViewerHandler::getInstance()->getChannels();
        $general['clientlist'] = TeamSpeakViewerHandler::getInstance()->getClients();
        return $general;
    }
}