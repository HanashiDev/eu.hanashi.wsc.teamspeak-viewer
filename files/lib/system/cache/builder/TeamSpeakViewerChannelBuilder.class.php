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
            $channel = $this->getChannelByID($id);

            $topic = null;
            if (!empty($channelinfo[0]['channel_topic'])) {
                $topic = $channelinfo[0]['channel_topic'];
            }

            $description = null;
            if (!empty($channelinfo[0]['channel_description'])) {
                $description = nl2br($channelinfo[0]['channel_description']);
                $description = str_replace('<br />', '<br>', $description);
                $description = preg_replace('/\[b\](.*)\[\/b\]/iU', '<b>$1</b>', $description);
                $description = preg_replace('/\[i\](.*)\[\/i\]/iU', '<i>$1</i>', $description);
                $description = preg_replace('/\[u\](.*)\[\/u\]/iU', '<u>$1</u>', $description);
                $description = preg_replace('/\[color=(.*)\](.*)\[\/color\]/iU', '<font style="color:$1;">$2</font>', $description);
                $target = '';
                if (EXTERNAL_LINK_TARGET_BLANK) {
                    $target .= ' target="_blank"';
                }
                if (EXTERNAL_LINK_REL_NOFOLLOW) {
                    $target .= ' rel="nofollow"';
                }
                $description = preg_replace('/\[url=(.*)\](.*)\[\/url\]/iU', '<a href="$1"'.$target.'>$2</a>', $description);
                $description = preg_replace('/\[url\](.*)\[\/url\]/iU', '<a href="$1"'.$target.'>$1</a>', $description);
                $description = preg_replace('/\[img\](.*)\[\/img\]/iU', '<img src="$1">', $description);
                $description = preg_replace('/\[hr\]/iU', '<hr>', $description);
                $description = preg_replace('/\[size=(.*)\](.*)\[\/size\]/iU', '<font style="font-size:$1px;">$2</font>', $description);
                $description = preg_replace('/\[left\](.*)\[\/left\](?:<br>)/iU', '<p style="text-align:left;">$1</p>', $description);
                $description = preg_replace('/\[center\](.*)\[\/center\](?:<br>)/iU', '<p style="text-align:center;">$1</p>', $description);
                $description = preg_replace('/\[right\](.*)\[\/right\](?:<br>)/iU', '<p style="text-align:right;">$1</p>', $description);
                $description = preg_replace('/\[list\](?:<br>)(.*)\[\/list\]/siU', '<ul style="list-style-type:disc;padding-left:20px;">$1</ul>', $description);
                $description = preg_replace("/\[\*\](.*)(?:<br>)/", '<li>$1</li>',$description);
            }

            $totalClients = 0;
            if (!empty($channel['total_clients'])) {
                $totalClients = $channel['total_clients'];
            }
            
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
                'total_clients' => $totalClients,
                'channel_maxclients' => $channelinfo[0]['channel_maxclients'],
                'channel_codec_is_unencrypted' => $channelinfo[0]['channel_codec_is_unencrypted'],
                'channel_needed_talk_power' => $channelinfo[0]['channel_needed_talk_power']
            ];
        } catch (TeamSpeakException $e) {
            return [];
        }
    }

    private function getChannelByID($channelID) {
        $channellist = TeamSpeakViewerChannellistBuilder::getInstance()->getData();
        $channelTmp = null;
        foreach ($channellist as $channel) {
            if ($channel['cid'] == $channelID) {
                $channelTmp = $channel;
                break;
            }
        }
        return $channelTmp;
    }
}