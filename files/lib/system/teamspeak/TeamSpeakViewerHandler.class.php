<?php
namespace wcf\system\teamspeak;
use wcf\system\exception\TeamSpeakException;
use wcf\util\TeamSpeakUtil;

/**
 * Teamspeak Handler for this extension
 *
 * @author	Peter Lohse <hanashi@hanashi.eu>
 * @copyright	Hanashi
 * @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package	WoltLabSuite\Core\System\Teamspeak
 */
class TeamSpeakViewerHandler extends AbstractTeamSpeakHandler {
    /**
     * @inheritDoc
     */
    protected $teamspeakID = HANASHI_TEAMSPEAK_VIEWER_IDENTITY;

    /**
     * get channels in correct order
     * 
     * @return array
     */
    public function getChannels() {
        try {
            $channellist = $this->channellist(['-icon']);

            $channelListTmp = [];
            foreach ($channellist as $channel) {
                if ($channel['pid'] == 0) {
                    if (preg_match('/^(\[([a-z\*])[a-zA-Z0-9\*]?spacer([0-9a-zA-Z]+)?\])(.*)$/', $channel['channel_name'], $matches)) {
                        $channel['is_spacer'] = true;

                        $spacerType = strtoupper($matches[2]);
                        if ($spacerType == '*') $spacerType = 'Repeat';
                        $channel['spacer_type'] = $spacerType;

                        $channelName = $matches[4];
                        if ($spacerType == 'Repeat') {
                            $channelNameTmp = '';
                            for ($i = 0; $i < 100; $i++) {
                                $channelNameTmp .= $channelName;
                            }
                            $channelName = $channelNameTmp;
                        }
                        $channel['channel_name'] = $channelName;
                    } else {
                        $channel['is_spacer'] = false;
                    }

                    $channelListTmp[$channel['cid']] = $channel;
                    $channelListTmp[$channel['cid']]['childs'] = TeamSpeakUtil::getChilds($channellist, 'pid', $channel['cid']);
                }
            }

            return $channelListTmp;
        } catch (TeamSpeakException $e) {
            return [];
        }
    }
}