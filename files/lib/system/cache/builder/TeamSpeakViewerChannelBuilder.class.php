<?php
namespace wcf\system\cache\builder;
use wcf\system\exception\TeamSpeakException;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

class TeamSpeakViewerChannelBuilder extends AbstractCacheBuilder {
    /**
     * @inheritDoc
     */
    protected $maxLifetime = HANASHI_TEAMSPEAK_VIEWER_CACHE_INTERVAL;
    
    /**
	 * @inheritDoc
	 */
    protected function rebuild(array $parameters) {
        try {
            $id = $parameters[0];
            $channelinfo = TeamSpeakViewerHandler::getInstance()->channelinfo(['cid' => $id]);

            $topic = null;
            if (!empty($channelinfo[0]['channel_topic'])) {
                $topic = $channelinfo[0]['channel_topic'];
            }

            $description = null;
            if (!empty($channelinfo[0]['channel_description'])) {
                $description = $channelinfo[0]['channel_description'];
            }

            // TODO: client anzahl
            // TODO Moderated
            
            return [
                'channel_name' => $channelinfo[0]['channel_name'],
                'channel_codec' => $channelinfo[0]['channel_codec'],
                'channel_topic' => $topic,
                'channel_description' => $description,
                'channel_flag_permanent' => $channelinfo[0]['channel_flag_permanent'],
                'channel_flag_semi_permanent' => $channelinfo[0]['channel_flag_semi_permanent'],
                'channel_flag_default' => $channelinfo[0]['channel_flag_default'],
                'channel_flag_password' => $channelinfo[0]['channel_flag_password'],
                'channel_flag_maxclients_unlimited' => $channelinfo[0]['channel_flag_maxclients_unlimited'],
                'channel_maxclients' => $channelinfo[0]['channel_maxclients'],
                'channel_codec_is_unencrypted' => $channelinfo[0]['channel_codec_is_unencrypted'],
                'channel_needed_talk_power' => $channelinfo[0]['channel_needed_talk_power']
            ];
        } catch (TeamSpeakException $e) {
            return [];
        }
    }
}