<ul>
    {foreach from=$__childChannels item=channel}
        <li data-type="channel" data-id="{$channel['cid']}">
            <div class="channelWrapper" style="padding-left: {$channel['level'] * 20}px;">
                <div class="channelCollapse">
                    {if $channel['childs']|count > 0}
                        <span class="icon icon16 fa-caret-down"></span>
                    {/if}
                </div>
                <div class="channelSubscription">
                    <img src="{$__wcf->getPath()}images/teamspeak_viewer/channel_unsubscribed.svg">
                </div>
                <div class="channelName">
                    {assign var='linkParameters' value='1'}
                    {$channel['channel_name']}
                </div>
                <div class="channelIcons">
                    {if $channel['channel_icon_id'] != 0}
                        <img src="{$__wcf->getPath()}images/teamspeak_viewer/icon_{$channel['channel_icon_id']}.png">
                    {/if}
                </div>
            </div>
            {if !$clientlist[$channel['cid']]|empty}
                {include file='__teamSpeakViewerClients' __channelID=$channel['cid'] __level=$channel['level']}
            {/if}

            {if $channel['childs']|count > 0}
                {* {include file='__teamSpeakViewerChildChannels' __childChannels=$channel['childs'] __childPadding=20} *}
                {@$tsTemplate->showChannels($channel['childs'])}
            {/if}
        </li>
    {/foreach}
</ul>