<?php
namespace wcf\system\cache\builder;
use wcf\system\exception\TeamSpeakException;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\WCF;
use wcf\util\TeamSpeakUtil;

class TeamSpeakViewerMenuBuilder extends AbstractCacheBuilder {
    /**
     * @inheritDoc
     */
    protected $maxLifetime = HANASHI_TEAMSPEAK_VIEWER_CACHE_INTERVAL;
    
    /**
	 * @inheritDoc
	 */
    protected function rebuild(array $parameters) {
        try {
            $clientlist = TeamSpeakViewerHandler::getInstance()->clientlist(['-away', '-voice', '-groups']);
            $clientlist = TeamSpeakUtil::arraySort($clientlist, 'client_nickname');

            $channellist = TeamSpeakViewerHandler::getInstance()->channellist();
            $channellistTmp = [];
            foreach ($channellist as $channel) {
                $channellistTmp[$channel['cid']] = $channel;
            }

            $clientlistTmp = [];
            foreach ($clientlist as $client) {
                if ($client['client_type'] == 1 && !HANASHI_TEAMSPEAK_VIEWER_SHOW_QUERY) continue;

                $client['channel'] = 'Unknown';
                if (isset($channellistTmp[$client['cid']])) {
                    $client['channel'] = $channellistTmp[$client['cid']]['channel_name'];
                }
                $clientlistTmp[] = $client;
            }

            return $clientlistTmp;
        } catch (TeamSpeakException $e) {
            if (ENABLE_DEBUG_MODE) {
				throw $e;
			}
            return [];
        }
    }
}