{if HANASHI_TEAMSPEAK_VIEWER_SHOW_CONNECT_BUTTON && HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}
    {capture append='contentHeaderNavigation'}
        <li><a href="{$teamspeakLink}" class="button"><span class="icon icon16 fa-server"></span> <span>Jetzt verbinden</span></a></li>
    {/capture}
{/if}

{include file='header'}

<div class="section teamspeakViewerOverview">
    <section class="section">
        <h2 class="sectionTitle">Channelliste</h2>
        <ul>
            <li data-type="server" data-id="">
                <div class="channelWrapper">
                    <div class="channelCollapse">
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/server.svg">
                    </div>
                    <div class="channelName">
                        {$serverinfo['virtualserver_name']}
                    </div>
                    <div class="channelIcons">
                        {* TODO: Server Icon *}
                        {* {if $channel['channel_icon_id'] != 0}
                            <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$channel['channel_icon_id']}.png">
                        {/if} *}
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
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/channel_unsubscribed.svg">
                            </div>
                        {/if}
                        <div class="channelName{if $channel['is_spacer']} channelSpacer{$channel['spacer_type']}{/if}">
                            {$channel['channel_name']}
                        </div>
                        <div class="channelIcons">
                            {if $channel['more_infos']['channel_flag_default'] == 1}
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/default.svg">
                            {/if}
                            {if $channel['more_infos']['channel_flag_password'] == 1}
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/register.svg">
                            {/if}
                            {if $channel['more_infos']['channel_needed_talk_power'] > 0}
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/moderated.svg">
                            {/if}
                            {if $channel['channel_icon_id'] != 0}
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$channel['channel_icon_id']}.png">
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
        {* TODO: Hostbanner ausblenden wenn nicht vorhanden *}
        <dl>
            <dt>Hostbanner:</dt>
            <dd>
                {* TODO: Automatisch aktualisieren: virtualserver_hostbanner_gfx_interval *}
                <a href="{$serverinfo['virtualserver_hostbanner_url']}">
                    <img src="{$serverinfo['virtualserver_hostbanner_gfx_url']}" id="HostBanner">
                </a>
            </dd>
        </dl>
        {if HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}
            <dl>
                <dt>Adresse:</dt>
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
                    <dt>Passwort:</dt>
                    <dd>{HANASHI_TEAMSPEAK_VIEWER_PASSWORD}</dd>
                </dl>
            {/if}
        {/if}
        <dl>
            <dt>Version:</dt>
            <dd>{$serverinfo['virtualserver_version']} on {$serverinfo['virtualserver_platform']}</dd>
        </dl>
        <dl>
            <dt>Online seit:</dt>
            <dd>{@(TIME_NOW - $serverinfo['virtualserver_uptime'])|time}</dd>
        </dl>
        <dl>
            <dt>Aktuelle Clients:</dt>
            <dd>
                {* TODO: reservierte Clients *}
                {$serverinfo['virtualserver_clientsonline']} / {$serverinfo['virtualserver_maxclients']}
            </dd>
        </dl>
        <dl>
            <dt>Aktuelle Channel:</dt>
            <dd>{$serverinfo['virtualserver_channelsonline']}</dd>
        </dl>
    </section>
</div>

<script data-relocate="true">
    require(['WoltLabSuite/Core/TeamSpeak/Viewer'], function(TeamSpeakViewer) {
        var options = {
            showData: {if HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}true{else}false{/if},
            showPassword: {if HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD}true{else}false{/if},
            serverPassword: '{if HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD && !HANASHI_TEAMSPEAK_VIEWER_PASSWORD|empty}{HANASHI_TEAMSPEAK_VIEWER_PASSWORD}{/if}'
        };

        new TeamSpeakViewer(options);
    });
</script>

{include file='footer'}