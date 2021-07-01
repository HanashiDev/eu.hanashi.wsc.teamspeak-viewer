{foreach from=$clientlist item=client}
    <li>
        <div class="box48">
            <div style="flex: 1 1 auto;" class="TeamSpeakClient">
                {if $client['client_away'] == 1}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/away.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_output_hardware'] == 0}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/hardware_output_muted.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_output_muted'] == 1}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/output_muted.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_input_hardware'] == 0}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/hardware_input_muted.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_input_muted'] == 1}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/input_muted.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_flag_talking'] == 1 && $client['client_is_channel_commander'] == 1}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_commander_on.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_flag_talking'] == 0 && $client['client_is_channel_commander'] == 1}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_commander_off.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_flag_talking'] == 1}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_on.svg?v={@LAST_UPDATE_TIME}">
                {else if $client['client_flag_talking'] == 0}
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/player_off.svg?v={@LAST_UPDATE_TIME}">
                {/if}
                {$client['client_nickname']}
            </div>
            <div style="flex: 0 0 auto">
                {$client['channel']}
            </div>
        </div>
    </li>
{/foreach}