{if HANASHI_TEAMSPEAK_VIEWER_SHOW_CONNECT_BUTTON && HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}
    {capture append='contentHeaderNavigation'}
        <li><a href="{$teamspeakLink}" class="button"><span class="icon icon16 fa-server"></span> <span>Jetzt verbinden</span></a></li>
    {/capture}
{/if}

{include file='header'}

<div class="teamspeakViewerOverview">
    <section class="section">
        <h2 class="sectionTitle">Channelliste</h2>
        <ul>
            <li>
                <div class="channelWrapper">
                    <div class="channelCollapse">
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/server.svg">
                    </div>
                    <div class="channelName">
                        {$serverinfo[0]['virtualserver_name']}
                    </div>
                    <div class="channelIcons">
                        {* {if $channel['channel_icon_id'] != 0}
                            <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$channel['channel_icon_id']}.png">
                        {/if} *}
                    </div>
                </div>
            </li>
            {foreach from=$channellist item=channel}
                <li>
                    <div class="channelWrapper">
                        <div class="channelCollapse">
                            {if $channel['childs']|count > 0}
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
                            {if $channel['channel_icon_id'] != 0}
                                <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$channel['channel_icon_id']}.png">
                            {/if}
                        </div>
                    </div>
                    {if !$clientlist[$channel['cid']]|empty}
                        {include file='__teamSpeakViewerClients' __channelID=$channel['cid'] __level=0}
                    {/if}

                    {if $channel['childs']|count > 0}
                        {* {include file='__teamSpeakViewerChildChannels' __childChannels=$channel['childs'] __childPadding=20 __level=1} *}
                        {@$tsTemplate->showChannels($channel['childs'])}
                    {/if}
                </li>
            {/foreach}
        </ul>
    </section>
    <section class="section">
        <h2 class="sectionTitle">{$serverinfo[0]['virtualserver_name']}</h2>
        <dl>
            <dt>Server-Banner:</dt>
            <dd>
                {* TODO: Automatisch aktualisieren: virtualserver_hostbanner_gfx_interval *}
                <a href="{$serverinfo[0]['virtualserver_hostbanner_url']}">
                    <img src="{$serverinfo[0]['virtualserver_hostbanner_gfx_url']}" style="max-height: 300px;">
                </a>
            </dd>
        </dl>
        {if HANASHI_TEAMSPEAK_VIEWER_SHOW_DATA}
            <dl>
                <dt>Adresse:</dt>
                <dd>
                    {if HANASHI_TEAMSPEAK_VIEWER_ADDRESS|empty}
                        {$teamspeakObj->hostname}
                    {else}
                        {HANASHI_TEAMSPEAK_VIEWER_ADDRESS}
                    {/if}
                </dd>
            </dl>
            <dl>
                <dt>Port:</dt>
                <dd>
                    {if HANASHI_TEAMSPEAK_VIEWER_PORT|empty}
                        {$teamspeakObj->virtualServerPort}
                    {else}
                        {HANASHI_TEAMSPEAK_VIEWER_PORT}
                    {/if}
                </dd>
            </dl>
        {/if}
        {if $serverinfo[0]['virtualserver_flag_password'] == 1 && HANASHI_TEAMSPEAK_VIEWER_SHOW_PASSWORD && !HANASHI_TEAMSPEAK_VIEWER_PASSWORD|empty}
            <dl>
                <dt>Passwort:</dt>
                <dd>{HANASHI_TEAMSPEAK_VIEWER_PASSWORD}</dd>
            </dl>
        {/if}
        <dl>
            <dt>Version:</dt>
            <dd>{$serverinfo[0]['virtualserver_version']}</dd>
        </dl>
        <dl>
            <dt>Online seit:</dt>
            <dd>{@(TIME_NOW - $serverinfo[0]['virtualserver_uptime'])|time}</dd>
        </dl>
        <dl>
            <dt>Aktuelle Clients:</dt>
            <dd>
                {* {assign var='clientsOnline' value=$serverinfo[0]['virtualserver_clientsonline']}
                {if !HANASHI_TEAMSPEAK_VIEWER_SHOW_QUERY}
                    {assign var='clientsOnline' value=$serverinfo[0]['virtualserver_clientsonline'] - 1}
                {/if} *}
                {$serverinfo[0]['virtualserver_clientsonline']}/{$serverinfo[0]['virtualserver_maxclients']}
            </dd>
        </dl>
        <dl>
            <dt>Aktuelle Channel:</dt>
            <dd>{$serverinfo[0]['virtualserver_channelsonline']}</dd>
        </dl>
    </section>
</div>

{include file='footer'}