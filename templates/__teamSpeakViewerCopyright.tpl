{event name='copyright'}

{if $showTeamSpeakViewerCopyright|isset && $showTeamSpeakViewerCopyright}
    <div class="copyright">
        <a href="https://hanashi.eu/"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}>{lang}wcf.copyright.teamSpeakViewer{/lang}</a>
    </div>
{/if}