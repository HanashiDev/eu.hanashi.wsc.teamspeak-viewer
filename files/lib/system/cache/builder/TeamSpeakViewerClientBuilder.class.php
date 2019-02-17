<?php
namespace wcf\system\cache\builder;
use wcf\system\exception\TeamSpeakException;
use wcf\system\teamspeak\TeamSpeakViewerHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

class TeamSpeakViewerClientBuilder extends AbstractCacheBuilder {
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
            $clientinfo = TeamSpeakViewerHandler::getInstance()->clientinfo(['clid' => $id]);
            $servergrouplist = TeamSpeakViewerHandler::getInstance()->getServergroups();
            $channelgrouplist = TeamSpeakViewerHandler::getInstance()->getChannelgroups();
            if (count($clientinfo) == 0) return [];

            $avatar = false;
            if (!empty($clientinfo[0]['client_flag_avatar'])) {
                $this->downloadAvatar($clientinfo[0]['client_base64HashClientUID']);
                $avatar = true;
            }

            $request = '';
            if (!empty($clientinfo[0]['client_talk_request_msg'])) {
                $request = $clientinfo[0]['client_talk_request_msg'];
            }

            $client_description = null;
            if (!empty($clientinfo[0]['client_description'])) {
                $client_description = $clientinfo[0]['client_description'];
            }

            $clientServerGroupsSplitted = explode(',', $clientinfo[0]['client_servergroups']);
            $serverGroupsTmp = [];
            foreach ($clientServerGroupsSplitted as $clientServerGroupID) {
                if (!empty($servergrouplist[$clientServerGroupID])) {
                    $serverGroupsTmp[] = $servergrouplist[$clientServerGroupID];
                }
            }

            $channelGroupTmp = [];
            if (!empty($channelgrouplist[$clientinfo[0]['client_channel_group_id']])) {
                $channelGroup = $channelgrouplist[$clientinfo[0]['client_channel_group_id']];
                if ($clientinfo[0]['cid'] != $clientinfo[0]['client_channel_group_inherited_channel_id']) {
                    $inherited = TeamSpeakViewerChannelBuilder::getInstance()->getData([$clientinfo[0]['client_channel_group_inherited_channel_id']]);
                    // TODO: Sprachvariable
                    $channelGroup['name'] = $channelGroup['name'].' [Geerbt von: '.$inherited['channel_name'].']';
                }
                $channelGroupTmp[] = $channelGroup;
            }
            
            return [
                'client_nickname' => $clientinfo[0]['client_nickname'],
                'client_version' => $clientinfo[0]['client_version'],
                'client_platform' => $clientinfo[0]['client_platform'],
                'connection_connected_time' => (TIME_NOW * 1000) - $clientinfo[0]['connection_connected_time'],
                'client_servergroups' => $serverGroupsTmp,
                'client_channel_group_id' => $channelGroupTmp,
                'cid' => $clientinfo[0]['cid'],
                'client_channel_group_inherited_channel_id' => $clientinfo[0]['client_channel_group_inherited_channel_id'],
                'avatar' => $avatar,
                'client_base64HashClientUID' => $clientinfo[0]['client_base64HashClientUID'],
                'client_input_muted' => $clientinfo[0]['client_input_muted'],
                'client_output_muted' => $clientinfo[0]['client_output_muted'],
                'client_outputonly_muted' => $clientinfo[0]['client_outputonly_muted'],
                'client_input_hardware' => $clientinfo[0]['client_input_hardware'],
                'client_output_hardware' => $clientinfo[0]['client_output_hardware'],
                'client_is_recording' => $clientinfo[0]['client_is_recording'],
                'client_away' => $clientinfo[0]['client_away'],
                'client_is_talker' => $clientinfo[0]['client_is_talker'],
                'client_icon_id' => $clientinfo[0]['client_icon_id'],
                'client_talk_request' => $clientinfo[0]['client_talk_request'],
                'client_talk_request_msg' => $request,
                'client_description' => $client_description
            ];
        } catch (TeamSpeakException $e) {
            return [];
        }
    }

    protected function downloadAvatar($clientUID) {
        $tmpFile = TeamSpeakViewerHandler::getInstance()->downloadFile(0, 'avatar_'.$clientUID);
        FileUtil::makePath(WCF_DIR.'images/teamspeak_viewer/avatar/');
        rename($tmpFile, WCF_DIR . 'images/teamspeak_viewer/avatar/avatar_'.$clientUID.'.png');
    }
}