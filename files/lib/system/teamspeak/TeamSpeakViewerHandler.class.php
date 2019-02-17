<?php
namespace wcf\system\teamspeak;
use wcf\system\cache\builder\TeamSpeakViewerChannelBuilder;
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
            $channellist = $this->channellist(['-icon',  '-icon', '-flags', '-voice']);

            $channelListTmp = [];
            foreach ($channellist as $channel) {
                if ($channel['pid'] == 0) {
                    // Berechnen ob Channel spacer ist
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

                    // Icon checken
                    if ($channel['channel_icon_id'] != 0) {
                        $channel['channel_icon_id'] = TeamSpeakUtil::getCorrectIconID($channel['channel_icon_id']);
                        $this->checkIcon($channel['channel_icon_id']);
                    }

                    $channelListTmp[$channel['cid']] = $channel;
                    $channelListTmp[$channel['cid']]['childs'] = TeamSpeakUtil::getChilds($channellist, 'pid', $channel['cid']);
                }
            }

            return $channelListTmp;
        } catch (TeamSpeakException $e) {
            if (ENABLE_DEBUG_MODE) {
				throw $e;
			}
            return [];
        }
    }

    public function getServergroups() {
        try {
            $servergrouplist = $this->servergrouplist();

            $servergrouplistTmp = [];
            foreach ($servergrouplist as $servergroup) {
                if ($servergroup['iconid'] == 0) {
                    $servergroup['iconid'] = null;
                } else if ($servergroup['iconid'] == 100) {
                    $servergroup['iconid'] = 'group_100.svg';
                } else if ($servergroup['iconid'] == 200) {
                    $servergroup['iconid'] = 'group_200.svg';
                } else if ($servergroup['iconid'] == 300) {
                    $servergroup['iconid'] = 'group_300.svg';
                } else if ($servergroup['iconid'] == 500) {
                    $servergroup['iconid'] = 'group_500.svg';
                } else if ($servergroup['iconid'] == 600) {
                    $servergroup['iconid'] = 'group_600.svg';
                } else {
                    $servergroup['iconid'] = TeamSpeakUtil::getCorrectIconID($servergroup['iconid']);
                    $this->checkIcon($servergroup['iconid']);
                    $servergroup['iconid'] = 'icon_'.$servergroup['iconid'].'.png';
                }
                $servergrouplistTmp[$servergroup['sgid']] = $servergroup;
            }
            
            return $servergrouplistTmp;
        } catch (TeamSpeakException $e) {
            if (ENABLE_DEBUG_MODE) {
				throw $e;
			}
            return [];
        }
    }

    public function getChannelgroups() {
        try {
            $channelgrouplist = $this->channelgrouplist();

            $channelgrouplistTmp = [];
            foreach ($channelgrouplist as $channelgroup) {
                if ($channelgroup['iconid'] == 0) {
                    $channelgroup['iconid'] = null;
                } else if ($channelgroup['iconid'] == 100) {
                    $channelgroup['iconid'] = 'group_100.svg';
                } else if ($channelgroup['iconid'] == 200) {
                    $channelgroup['iconid'] = 'group_200.svg';
                } else if ($channelgroup['iconid'] == 300) {
                    $channelgroup['iconid'] = 'group_300.svg';
                } else if ($channelgroup['iconid'] == 500) {
                    $channelgroup['iconid'] = 'group_500.svg';
                } else if ($channelgroup['iconid'] == 600) {
                    $channelgroup['iconid'] = 'group_600.svg';
                } else {
                    $channelgroup['iconid'] = TeamSpeakUtil::getCorrectIconID($channelgroup['iconid']);
                    $this->checkIcon($channelgroup['iconid']);
                    $channelgroup['iconid'] = 'icon_'.$channelgroup['iconid'].'.png';
                }
                $channelgrouplistTmp[$channelgroup['cgid']] = $channelgroup;
            }
            
            return $channelgrouplistTmp;
        } catch (TeamSpeakException $e) {
            if (ENABLE_DEBUG_MODE) {
				throw $e;
			}
            return [];
        }
    }

    public function getClients() {
        try {
            $clientlist = $this->clientlist(['-away', '-voice', '-groups', '-badges']);
            $servergrouplist = $this->getServergroups();
            $channelgrouplist = $this->getChannelgroups();

            $sort = [];
            foreach($clientlist as $key => $value) {
                $sort['client_nickname'][$key] = $value['client_nickname'];
                $sort['client_talk_power'][$key] = $value['client_talk_power'];
            }
            array_multisort($sort['client_talk_power'], SORT_DESC, $sort['client_nickname'], SORT_ASC, $clientlist);

            $clientListTmp = [];
            foreach ($clientlist as $client) {
                if (!HANASHI_TEAMSPEAK_VIEWER_SHOW_QUERY && $client['client_type'] == 1) continue;
                if (!empty($client['client_badges'])) {
                    $badgesArr = explode('=', $client['client_badges']);
                    if (!empty($badgesArr[1])) {
                        $badges = explode(',', $badgesArr[1]);
                        $badgesTmp = [];
                        foreach ($badges as $badge) {
                            $badgeTmp = $this->badge($badge);
                            if (empty($badgeTmp)) continue;
                            $badgesTmp[] = $badgeTmp;
                        }
                        $client['client_badges'] = $badgesTmp;
                        // $client['client_badges'] = explode(',', $badgesArr[1]);
                    }
                }

                $servergroupIDs = explode(',', $client['client_servergroups']);
                $groupIcons = [];
                foreach ($servergroupIDs as $servergroupID) {
                    if (!empty($servergrouplist[$servergroupID]['iconid'])) {
                        $groupIcons[] = $servergrouplist[$servergroupID]['iconid'];
                    }
                }
                $client['client_servergroups'] = $groupIcons;

                if (!empty($channelgrouplist[$client['client_channel_group_id']]['iconid'])) {
                    $client['client_channel_group_id'] = $channelgrouplist[$client['client_channel_group_id']]['iconid'];
                } else {
                    $client['client_channel_group_id'] = null;
                }

                $clientListTmp[$client['cid']][] = $client;
            }

            return $clientListTmp;
        } catch (TeamSpeakException $e) {
            if (ENABLE_DEBUG_MODE) {
				throw $e;
			}
            return [];
        }
    }

    public function checkIcon($iconID) {
        $filename = 'icon_'.$iconID;
        if (!file_exists(WCF_DIR . 'images/teamspeak_viewer/'.$filename.'.png')) {
            $tmpFile = $this->downloadFile(0, $filename);
            rename($tmpFile, WCF_DIR . 'images/teamspeak_viewer/'.$filename.'.png');
        }
    }

    public function badge($guid) {
        $badges = [
            '1cb07348-34a4-4741-b50f-c41e584370f7' => 'addon_author.svg',
            '50bbdbc8-0f2a-46eb-9808-602225b49627' => 'gamescom_2016.svg',
            'd95f9901-c42d-4bac-8849-7164fd9e2310' => 'paris_gamesweek_2016.svg',
            '62444179-0d99-42ba-a45c-c6b1557d079a' => 'gamescom_2014.svg',
            'd95f9901-c42d-4bac-8849-7164fd9e2310' => 'paris_gamesweek_2014.svg',
            '450f81c1-ab41-4211-a338-222fa94ed157' => 'addon_author_bronze.svg',
            'c9e97536-5a2d-4c8e-a135-af404587a472' => 'addon_author_silver.svg',
            '94ec66de-5940-4e38-b002-970df0cf6c94' => 'addon_author_gold.svg',
            '534c9582-ab02-4267-aec6-2d94361daa2a' => 'gamescom_2017.svg',
            '34dbfa8f-bd27-494c-aa08-a312fc0bb240' => 'hero_2017.svg',
            '7d9fa2b1-b6fa-47ad-9838-c239a4ddd116' => 'mifcom.svg',
            'f81ad44d-e931-47d1-a3ef-5fd160217cf8' => '4netplayers.svg',
            'f22c22f1-8e2d-4d99-8de9-f352dc26ac5b' => 'rbtv.svg',
            '64221fd1-706c-4bb2-ba55-996c39effa79' => 'TS-OG.svg',
            'c3f823eb-5d5c-40f9-9dbd-3437d59a539d' => 'TS-2018.svg',
            '935e5a2a-954a-44ca-aa7a-55c79285b601' => 'E3-2018.svg',
            '4eef1ecf-a0ea-423d-bfd0-496543a00305' => 'gamescom_2018.svg',
            '24512806-f886-4440-b579-9e26e4219ef6' => 'gamescom_2018_played.svg',
            'b9c7d6ad-5b99-40fb-988c-1d02ab6cc130' => 'met_tim.svg',
            '6b187e83-873b-46b0-b2c2-a31af15e76a4' => 'cap_red.svg'
        ];
        if (empty($badges[$guid])) return;
        return $badges[$guid];
    }
}