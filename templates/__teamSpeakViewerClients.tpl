<ul>
    {foreach from=$clientlist[$channel['cid']] item=client}
        <li data-type="client" data-id="{$client['clid']}">
            <div class="channelWrapper" style="padding-left: {($__level * 20) + 20}px;">
                <div class="channelCollapse"></div>
                <div class="channelSubscription">
                    {if $client['client_away'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/away.svg">
                    {else if $client['client_output_hardware'] == 0}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/hardware_output_muted.svg">
                    {else if $client['client_output_muted'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/output_muted.svg">
                    {else if $client['client_input_hardware'] == 0}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/hardware_input_muted.svg">
                    {else if $client['client_input_muted'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/input_muted.svg">
                    {else if $client['client_flag_talking'] == 1 && $client['client_is_channel_commander'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_commander_on.svg">
                    {else if $client['client_flag_talking'] == 0 && $client['client_is_channel_commander'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_commander_off.svg">
                    {else if $client['client_flag_talking'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_on.svg">
                    {else if $client['client_flag_talking'] == 0}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_off.svg">
                    {/if}
                </div>
                <div class="channelName{if $client['client_is_recording'] == 1} recording{/if}">
                    {if $client['client_is_recording'] == 1}
                        ***
                    {/if}
                    {$client['client_nickname']}
                    {if $client['client_is_recording'] == 1}
                        *** [AUFNAHME]
                    {/if}
                </div>
                <div class="channelIcons">
                    {if !$client['client_badges']|empty}
                        {foreach from=$client['client_badges'] item=badge}
                            <img src="{$__wcf->getPath()}images/teamspeak_viewer/badges/{$badge}">
                        {/foreach}
                    {/if}
                    {if $client['client_is_priority_speaker'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/capture.svg">
                    {/if}
                    {if $client['client_is_talker'] == 1}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/is_talker.svg">
                    {/if}
                    {if !$client['client_channel_group_id']|empty}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/{$client['client_channel_group_id']}">
                    {/if}
                    {foreach from=$client['client_servergroups'] item=$servergroup}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/{$servergroup}">
                    {/foreach}
                    {* TODO: Client Icon ID *}
                </div>
            </div>
        </li>
    {/foreach}
</ul>