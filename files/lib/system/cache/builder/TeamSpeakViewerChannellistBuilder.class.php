<?php
namespace wcf\system\cache\builder;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\WCF;

class TeamSpeakViewerChannellistBuilder extends AbstractCacheBuilder {
    /**
     * @inheritDoc
     */
    protected $maxLifetime = HANASHI_TEAMSPEAK_VIEWER_CACHE_INTERVAL;
    
    /**
	 * @inheritDoc
	 */
    protected function rebuild(array $parameters) {
        return TeamSpeakViewerHandler::getInstance()->channellist();
    }
}