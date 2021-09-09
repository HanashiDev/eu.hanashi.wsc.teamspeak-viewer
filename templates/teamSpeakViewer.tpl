{if HANASHI_TEAMSPEAK_VIEWER_SHOW_CONNECT_BUTTON && HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA && !$serverinfo|empty}
    {capture append='contentHeaderNavigation'}
        <li><a href="{$teamspeakLink}" class="button"><span class="icon icon16 fa-server"></span> <span>{lang}wcf.page.teamSpeakViewer.connectNow{/lang}</span></a></li>
    {/capture}
{/if}

{include file='header'}

{if !$serverinfo|empty}
    <div class="section teamspeakViewerOverview">
        <section class="section">
            <h2 class="sectionTitle">{lang}wcf.page.teamSpeakViewer.channellist{/lang}</h2>
            <ul>
                <li data-type="server" data-id="">
                    <div class="channelWrapper">
                        <div class="channelCollapse">
                            <img src="{$__wcf->getPath()}images/teamspeak_viewer/server.svg?v={@LAST_UPDATE_TIME}">
                        </div>
                        <div class="channelName">
                            {$serverinfo['virtualserver_name']}
                        </div>
                        <div class="channelIcons">
                            {if $serverinfo['virtualserver_icon_id'] != 0}
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$serverinfo['virtualserver_icon_id']}.png?v={@LAST_UPDATE_TIME}">
                            {/if}
                        </div>
                    </div>
                </li>
                {foreach from=$channellist item=channel}
                    <li data-type="channel" data-id="{$channel['cid']}">
                        <div class="channelWrapper">
                            <div class="channelCollapse">
                                {if $channel['childs']|count > 0 || (!$clientlist[$channel['cid']]|empty) && $clientlist[$channel['cid']]|count > 0}
                                    <span class="icon icon16 fa-caret-down"></span>
                                {/if}
                            </div>
                            {if !$channel['is_spacer']}
                                <div class="channelSubscription">
                                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/channel_unsubscribed.svg?v={@LAST_UPDATE_TIME}">
                                </div>
                            {/if}
                            <div class="channelName{if $channel['is_spacer']} channelSpacer{$channel['spacer_type']}{/if}">
                                {$channel['channel_name']}
                            </div>
                            <div class="channelIcons">
                                {if $channel['channel_flag_default'] == 1}
                                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/default.svg?v={@LAST_UPDATE_TIME}">
                                {/if}
                                {if $channel['channel_flag_password'] == 1}
                                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/register.svg?v={@LAST_UPDATE_TIME}">
                                {/if}
                                {if $channel['channel_needed_talk_power'] > 0}
                                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/moderated.svg?v={@LAST_UPDATE_TIME}">
                                {/if}
                                {if $channel['channel_icon_id'] != 0}
                                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$channel['channel_icon_id']}.png?v={@LAST_UPDATE_TIME}">
                                {/if}
                            </div>
                        </div>
                        {if !$clientlist[$channel['cid']]|empty}
                            {include file='__teamSpeakViewerClients' __channelID=$channel['cid'] __level=0}
                        {/if}

                        {if $channel['childs']|count > 0}
                            {@$tsTemplate->showChannels($channel['childs'])}
                        {/if}
                    </li>
                {/foreach}
            </ul>
        </section>
        <section class="section" id="TeamSpeakServerInfo">
            <h2 class="sectionTitle">{$serverinfo['virtualserver_name']}</h2>
            {if !$serverinfo['virtualserver_hostbanner_gfx_url']|empty}
                <dl>
                    <dt>{lang}wcf.js.teamSpeakViewer.hostbannerTitle{/lang}</dt>
                    <dd>
                        {* TODO: Automatisch aktualisieren: virtualserver_hostbanner_gfx_interval *}
                        {if $serverinfo['virtualserver_hostbanner_url']|empty}
                            <img src="{$serverinfo['virtualserver_hostbanner_gfx_url']}" id="HostBanner">
                        {else}
                            <a href="{$serverinfo['virtualserver_hostbanner_url']}">
                                <img src="{$serverinfo['virtualserver_hostbanner_gfx_url']}" id="HostBanner">
                            </a>
                        {/if}
                    </dd>
                </dl>
            {/if}
            {if HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}
                <dl>
                    <dt>{lang}wcf.js.teamSpeakViewer.addressTitle{/lang}</dt>
                    <dd>
                        {assign var='teamSpeakAddress' value=$serverinfo['hostname']}
                        {if $serverinfo['port'] != 9987}
                            {capture append=teamSpeakAddress}:{$serverinfo['port']}{/capture}
                        {/if}
                        {$teamSpeakAddress}
                    </dd>
                </dl>
                {if $serverinfo['virtualserver_flag_password'] == 1 && HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD && !HANASHI_TEAMSPEAK_VIEWER_PASSWORD|empty}
                    <dl>
                        <dt>{lang}wcf.js.teamSpeakViewer.passwordTitle{/lang}</dt>
                        <dd>{HANASHI_TEAMSPEAK_VIEWER_PASSWORD}</dd>
                    </dl>
                {/if}
            {/if}
            <dl>
                <dt>{lang}wcf.js.teamSpeakViewer.versionTitle{/lang}</dt>
                <dd>{$serverinfo['virtualserver_version']} on {$serverinfo['virtualserver_platform']}</dd>
            </dl>
            <dl>
                <dt>{lang}wcf.js.teamSpeakViewer.onlineSinceTitle{/lang}</dt>
                <dd>{@(TIME_NOW - $serverinfo['virtualserver_uptime'])|time}</dd>
            </dl>
            <dl>
                <dt>{lang}wcf.js.teamSpeakViewer.actualClientsTitle{/lang}</dt>
                <dd>
                    {$serverinfo['virtualserver_clientsonline']} / {$serverinfo['virtualserver_maxclients']}
                    {if $serverinfo['virtualserver_reserved_slots'] > 0}
                        (<div class="reserved">-{$serverinfo['virtualserver_reserved_slots']} reserved</div>)
                    {/if}
                </dd>
            </dl>
            <dl>
                <dt>{lang}wcf.js.teamSpeakViewer.actualChannelsTitle{/lang}</dt>
                <dd>{$serverinfo['virtualserver_channelsonline']}</dd>
            </dl>
        </section>
    </div>

    <script data-relocate="true">
        require(['Language', 'WoltLabSuite/Core/TeamSpeak/Viewer'], function(Language, TeamSpeakViewer) {
            Language.addObject({
                'wcf.js.teamSpeakViewer.version': '{lang}wcf.js.teamSpeakViewer.version{/lang}',
                'wcf.js.teamSpeakViewer.versionContent': '{capture assign=versionContent}{lang __literal=true}wcf.js.teamSpeakViewer.versionContent{/lang}{/capture}{@$versionContent|encodeJS}',
                'wcf.js.teamSpeakViewer.onlineSinceTitle': '{lang}wcf.js.teamSpeakViewer.onlineSinceTitle{/lang}',
                'wcf.js.teamSpeakViewer.descriptionTitle': '{lang}wcf.js.teamSpeakViewer.descriptionTitle{/lang}',
                'wcf.js.teamSpeakViewer.badgesTitle': '{lang}wcf.js.teamSpeakViewer.badgesTitle{/lang}',
                'wcf.js.teamSpeakViewer.servergroupsTitle': '{lang}wcf.js.teamSpeakViewer.servergroupsTitle{/lang}',
                'wcf.js.teamSpeakViewer.channelgroupTitle': '{lang}wcf.js.teamSpeakViewer.channelgroupTitle{/lang}',
                'wcf.js.teamSpeakViewer.avatarTitle': '{lang}wcf.js.teamSpeakViewer.avatarTitle{/lang}',
                'wcf.js.teamSpeakViewer.channelTopicTitle': '{lang}wcf.js.teamSpeakViewer.channelTopicTitle{/lang}',
                'wcf.js.teamSpeakViewer.codecUnknown': '{lang}wcf.js.teamSpeakViewer.codecUnknown{/lang}',
                'wcf.js.teamSpeakViewer.codec0': '{lang}wcf.js.teamSpeakViewer.codec0{/lang}',
                'wcf.js.teamSpeakViewer.codec1': '{lang}wcf.js.teamSpeakViewer.codec1{/lang}',
                'wcf.js.teamSpeakViewer.codec2': '{lang}wcf.js.teamSpeakViewer.codec2{/lang}',
                'wcf.js.teamSpeakViewer.codec3': '{lang}wcf.js.teamSpeakViewer.codec3{/lang}',
                'wcf.js.teamSpeakViewer.codec4': '{lang}wcf.js.teamSpeakViewer.codec4{/lang}',
                'wcf.js.teamSpeakViewer.codec5': '{lang}wcf.js.teamSpeakViewer.codec5{/lang}',
                'wcf.js.teamSpeakViewer.codecTitle': '{lang}wcf.js.teamSpeakViewer.codecTitle{/lang}',
                'wcf.js.teamSpeakViewer.flag_permanent': '{lang}wcf.js.teamSpeakViewer.flag_permanent{/lang}',
                'wcf.js.teamSpeakViewer.flag_semi_permanent': '{lang}wcf.js.teamSpeakViewer.flag_semi_permanent{/lang}',
                'wcf.js.teamSpeakViewer.flag_temporary': '{lang}wcf.js.teamSpeakViewer.flag_temporary{/lang}',
                'wcf.js.teamSpeakViewer.flag_default': '{lang}wcf.js.teamSpeakViewer.flag_default{/lang}',
                'wcf.js.teamSpeakViewer.flag_password': '{lang}wcf.js.teamSpeakViewer.flag_password{/lang}',
                'wcf.js.teamSpeakViewer.settingsTitle': '{lang}wcf.js.teamSpeakViewer.settingsTitle{/lang}',
                'wcf.js.teamSpeakViewer.unlimited': '{lang}wcf.js.teamSpeakViewer.unlimited{/lang}',
                'wcf.js.teamSpeakViewer.actualClientsTitle': '{lang}wcf.js.teamSpeakViewer.actualClientsTitle{/lang}',
                'wcf.js.teamSpeakViewer.moderatedTitle': '{lang}wcf.js.teamSpeakViewer.moderatedTitle{/lang}',
                'wcf.js.teamSpeakViewer.yes': '{lang}wcf.js.teamSpeakViewer.yes{/lang}',
                'wcf.js.teamSpeakViewer.hostbannerTitle': '{lang}wcf.js.teamSpeakViewer.hostbannerTitle{/lang}',
                'wcf.js.teamSpeakViewer.addressTitle': '{lang}wcf.js.teamSpeakViewer.addressTitle{/lang}',
                'wcf.js.teamSpeakViewer.passwordTitle': '{lang}wcf.js.teamSpeakViewer.passwordTitle{/lang}',
                'wcf.js.teamSpeakViewer.versionTitle': '{lang}wcf.js.teamSpeakViewer.versionTitle{/lang}',
                'wcf.js.teamSpeakViewer.actualChannelsTitle': '{lang}wcf.js.teamSpeakViewer.actualChannelsTitle{/lang}'
            });

            var options = {
                showData: {if HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}true{else}false{/if},
                showPassword: {if HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD}true{else}false{/if},
                serverPassword: '{if HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD && !HANASHI_TEAMSPEAK_VIEWER_PASSWORD|empty}{HANASHI_TEAMSPEAK_VIEWER_PASSWORD}{/if}'
            };

            new TeamSpeakViewer(options);
        });
    </script>
{else}
    <p class="info">{lang}wcf.page.teamSpeakViewer.notReachable{/lang}</p>
{/if}

{include file='footer'}