<?php

namespace wcf\system\teamspeak;

use wcf\system\cache\builder\TeamSpeakViewerChannelBuilder;
use wcf\system\exception\TeamSpeakException;
use wcf\util\TeamSpeakUtil;

/**
 * Teamspeak Handler for this extension
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package WoltLabSuite\Core\System\Teamspeak
 */
class TeamSpeakViewerHandler extends AbstractTeamSpeakHandler
{
    /**
     * @inheritDoc
     */
    protected $teamspeakID = HANASHI_TEAMSPEAK_VIEWER_IDENTITY;

    /**
     * get channels in correct order
     *
     * @return array
     */
    public function getChannels()
    {
        try {
            $channellist = $this->channellist(['-icon', '-flags', '-voice']);

            $channelListIconTmp = [];
            foreach ($channellist as $channel) {
                // Icon checken
                if ($channel['channel_icon_id'] != 0) {
                    $channel['channel_icon_id'] = TeamSpeakUtil::getCorrectIconID($channel['channel_icon_id']);
                    if (!$this->checkIcon($channel['channel_icon_id'])) {
                        $channel['channel_icon_id'] = 0;
                    }
                }
                $channelListIconTmp[] = $channel;
            }

            $channelListTmp = [];
            foreach ($channelListIconTmp as $channel) {
                if ($channel['pid'] == 0) {
                    // Berechnen ob Channel spacer ist
                    if (
                        preg_match(
                            '/^(\[([a-z\*])[a-zA-Z0-9\*]?spacer([0-9a-zA-Z]+)?\])(.*)$/',
                            $channel['channel_name'],
                            $matches
                        )
                    ) {
                        $channel['is_spacer'] = true;

                        $spacerType = strtoupper($matches[2]);
                        if ($spacerType == '*') {
                            $spacerType = 'Repeat';
                        }
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
                    $channelListTmp[$channel['cid']]['childs'] = TeamSpeakUtil::getChilds(
                        $channelListIconTmp,
                        'pid',
                        $channel['cid']
                    );
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

    public function getServergroups()
    {
        try {
            $servergrouplist = $this->servergrouplist();

            $servergrouplistTmp = [];
            foreach ($servergrouplist as $servergroup) {
                if ($servergroup['iconid'] == 0) {
                    $servergroup['iconid'] = null;
                } elseif ($servergroup['iconid'] == 100) {
                    $servergroup['iconid'] = 'group_100.svg';
                } elseif ($servergroup['iconid'] == 200) {
                    $servergroup['iconid'] = 'group_200.svg';
                } elseif ($servergroup['iconid'] == 300) {
                    $servergroup['iconid'] = 'group_300.svg';
                } elseif ($servergroup['iconid'] == 500) {
                    $servergroup['iconid'] = 'group_500.svg';
                } elseif ($servergroup['iconid'] == 600) {
                    $servergroup['iconid'] = 'group_600.svg';
                } else {
                    $servergroup['iconid'] = TeamSpeakUtil::getCorrectIconID($servergroup['iconid']);
                    if ($this->checkIcon($servergroup['iconid'])) {
                        $servergroup['iconid'] = 'icon_' . $servergroup['iconid'] . '.png';
                    } else {
                        $servergroup['iconid'] = 0;
                    }
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

    public function getChannelgroups()
    {
        try {
            $channelgrouplist = $this->channelgrouplist();

            $channelgrouplistTmp = [];
            foreach ($channelgrouplist as $channelgroup) {
                if ($channelgroup['iconid'] == 0) {
                    $channelgroup['iconid'] = null;
                } elseif ($channelgroup['iconid'] == 100) {
                    $channelgroup['iconid'] = 'group_100.svg';
                } elseif ($channelgroup['iconid'] == 200) {
                    $channelgroup['iconid'] = 'group_200.svg';
                } elseif ($channelgroup['iconid'] == 300) {
                    $channelgroup['iconid'] = 'group_300.svg';
                } elseif ($channelgroup['iconid'] == 500) {
                    $channelgroup['iconid'] = 'group_500.svg';
                } elseif ($channelgroup['iconid'] == 600) {
                    $channelgroup['iconid'] = 'group_600.svg';
                } else {
                    $channelgroup['iconid'] = TeamSpeakUtil::getCorrectIconID($channelgroup['iconid']);
                    if ($this->checkIcon($channelgroup['iconid'])) {
                        $channelgroup['iconid'] = 'icon_' . $channelgroup['iconid'] . '.png';
                    } else {
                        $channelgroup['iconid'] = 0;
                    }
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

    public function getClients()
    {
        try {
            $clientlist = $this->clientlist(['-away', '-voice', '-groups', '-badges']);
            $servergrouplist = $this->getServergroups();
            $channelgrouplist = $this->getChannelgroups();

            $sort = [];
            foreach ($clientlist as $key => $value) {
                $sort['client_nickname'][$key] = $value['client_nickname'];
                $sort['client_talk_power'][$key] = $value['client_talk_power'];
            }
            array_multisort($sort['client_talk_power'], SORT_DESC, $sort['client_nickname'], SORT_ASC, $clientlist);

            $clientListTmp = [];
            foreach ($clientlist as $client) {
                if (!HANASHI_TEAMSPEAK_VIEWER_SHOW_QUERY && $client['client_type'] == 1) {
                    continue;
                }
                if (!empty($client['client_badges'])) {
                    $clientBadgesTmp = explode(':', $client['client_badges']);
                    foreach ($clientBadgesTmp as $clientBadgeTmp) {
                        $badgesArr = explode('=', $clientBadgeTmp);
                        if (strtolower($badgesArr[0]) == 'overwolf') {
                            if ($badgesArr[1] == '1') {
                                $client['overwolf'] = true;
                            }
                        } elseif ($badgesArr[0] == 'badges') {
                            if (!empty($badgesArr[1])) {
                                $badges = explode(',', $badgesArr[1]);
                                $badgesTmp = [];
                                foreach ($badges as $badge) {
                                    $badgeTmp = $this->badge($badge);
                                    if (empty($badgeTmp)) {
                                        continue;
                                    }
                                    $badgesTmp[] = $badgeTmp;
                                }
                                $client['client_badges'] = $badgesTmp;
                            }
                        }
                    }
                    if (!is_array($client['client_badges'])) {
                        $client['client_badges'] = [];
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
                    $iconID = $channelgrouplist[$client['client_channel_group_id']]['iconid'];
                    $client['client_channel_group_id'] = $iconID;
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

    public function checkIcon($iconID)
    {
        $filename = 'icon_' . $iconID;
        if (!file_exists(WCF_DIR . 'images/teamspeak_viewer/' . $filename . '.png')) {
            try {
                $tmpFile = $this->downloadFile(0, $filename);
                rename($tmpFile, WCF_DIR . 'images/teamspeak_viewer/' . $filename . '.png');
            } catch (TeamSpeakException $e) {
                return false;
            }
        }
        return true;
    }

    public function badge($guid)
    {
        $badges = [
            '0d98391c-ecdf-4f26-931a-49bfd669cda7' => 'E_MindEgg.svg',
            '0005232e-538e-4cb2-93b6-d7d83e873829' => 'PietSmiet.svg',
            '05114019-6b46-4b13-b5a1-e5179ef69fb5' => 'rpg.svg',
            '089c7295-3aa2-48b0-b2f1-2dd1bec12caf' => 'E_TimeEgg.svg',
            '1a518885-520c-4f54-9f49-8b1acb674771' => 'Braindance.svg',
            '1aa375e8-7207-45bf-8b80-556bafafc834' => 'E_RealityEgg.svg',
            '1cb07348-34a4-4741-b50f-c41e584370f7' => 'addon_author.svg',
            '2bf80270-8efe-46dc-a472-3280a0479145' => 'Alpha.svg',
            '2c9698c1-1fec-4baa-a28f-4845f045f42f' => 'E_SoulEgg.svg',
            '4b0fd4f5-d456-4294-973d-853a1db5c7d8' => 'Valentines_Badge.svg',
            '4b27be5a-b92a-4b30-8b2d-14b59653f427' => '20_years.svg',
            '4eef1ecf-a0ea-423d-bfd0-496543a00305' => 'gamescom_2018.svg',
            '5f6d49e4-35c8-4809-8c81-6e71f7f749e9' => 'E_PowerEgg.svg',
            '6b187e83-873b-46b0-b2c2-a31af15e76a4' => 'cap_red.svg',
            '7a627d47-5496-4d68-83b5-2c4eafff9b30' => 'StaySafeStayHome.svg',
            '7d9fa2b1-b6fa-47ad-9838-c239a4ddd116' => 'mifcom.svg',
            '8c22fe26-30ac-4231-8b31-67d8a75c808a' => 'ox21.svg',
            '8dfa37ac-b40d-4466-b393-ff2184a9adf3' => 'OWL_Badge.svg',
            '9cd152a7-bf65-4ece-aeba-62d27678f79a' => 'CompWinnerBadge.svg',
            '9ea23c77-8755-4d82-b30c-92f4aac109ef' => 'wft.svg',
            '22b9ec39-7694-453e-864c-dfc7b1b0d7c7' => 'topper.svg',
            '34dbfa8f-bd27-494c-aa08-a312fc0bb240' => 'hero_2017.svg',
            '50bbdbc8-0f2a-46eb-9808-602225b49627' => 'gamescom_2016.svg',
            '56df5ce2-6c5a-4a24-90e2-29e497e26170' => 'DCL_Badge.svg',
            '87ccf9ea-67c9-45e5-adbc-77e210e6128a' => 'tim_irl.svg',
            '94ec66de-5940-4e38-b002-970df0cf6c94' => 'addon_author_gold.svg',
            '450f81c1-ab41-4211-a338-222fa94ed157' => 'addon_author_bronze.svg'
            '534c9582-ab02-4267-aec6-2d94361daa2a' => 'gamescom_2017.svg',
            '641a4d85-2351-482c-97a1-02fc3b6abbb5' => 'E_SpaceEgg.svg',
            '904e232c-f369-44db-87f7-5142e15620cc' => 'time_machine.svg',
            '935e5a2a-954a-44ca-aa7a-55c79285b601' => 'E3-2018.svg',
            '4086a249-a503-4f31-9e83-8a0a8e3089bd' => 'Tim-O-Lantern.svg',
            '64221fd1-706c-4bb2-ba55-996c39effa79' => 'TS-OG.svg',
            '205916f3-a953-4754-8905-bc15069b1f91' => 'Merch3.svg',
            '24512806-f886-4440-b579-9e26e4219ef6' => 'gamescom_2018_played.svg',
            '62444179-0d99-42ba-a45c-c6b1557d079a' => 'gamescom_2014.svg',
            '92801833-e721-4b7e-84d4-6c02dbb332b9' => 'Valentim2020.svg',
            'a676c708-da67-4784-ba7f-3fb7e8d2e865' => 'valentine21.svg',
            'b9c7d6ad-5b99-40fb-988c-1d02ab6cc130' => 'met_tim.svg',
            'b82a45a5-b235-4926-be77-de102222e5eb' => 'Gamescom19.svg',
            'be932556-dfa9-4dc6-afd0-98de0ab25777' => 'PolTeamgeist.svg',
            'c3f823eb-5d5c-40f9-9dbd-3437d59a539d' => 'TS-2018.svg',
            'c9e97536-5a2d-4c8e-a135-af404587a472' => 'addon_author_silver.svg',
            'c6480fe2-ee25-4ee8-9853-243652c8ec54' => 'Christmas2020.svg',
            'c2368518-3728-4260-bcd1-8b85e9f8984c' => 'Testing.svg',
            'cbf5aafd-2554-4053-80bb-0cf82ec0a430' => 'FeatureBadge_Lightbulb.svg',
            'ceee2445-4fbf-4f06-9421-286f0f4e875a' => 'pride.svg',
            'd4ea0251-ba46-4c1a-83b7-59db3f89e52c' => 'firework_2020.svg',
            'd95f9901-c42d-4bac-8849-7164fd9e2310' => 'paris_gamesweek_2016.svg',
            'd6062d9c-42a3-49c9-91dd-8c43a5a46805' => 'BugBadge_Splat.svg',
            'de7bd960-eb02-47e1-9ce2-a44f6e255d8f' => 'Happy_Holidays.svg"',
            'dfc70674-0fd0-431e-b3a1-edc32d7b09b2' => 'scill.svg'
            'ed85bdff-2a2b-4bea-a1a5-4d06fcc0d776' => 'Christmas.svg',
            'ef567ec5-f46e-4520-be07-6021023cf6bd' => 'Sponsorship.svg',
            'f22c22f1-8e2d-4d99-8de9-f352dc26ac5b' => 'rbtv.svg',
            'f81ad44d-e931-47d1-a3ef-5fd160217cf8' => '4netplayers.svg',
            'f85f7e21-753a-4566-b26a-4e3e9155d2ef' => 'Cyberpunk.svg',
            'fa3ece28-64df-431f-b1b3-90844bfdd2d9' => 'paris_gamesweek_2014.svg'
        ];
        if (empty($badges[$guid])) {
            return;
        }
        return $badges[$guid];
    }
}
