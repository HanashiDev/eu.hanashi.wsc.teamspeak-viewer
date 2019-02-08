<?php
namespace wcf\system\template;
use wcf\system\WCF;

class TeamSpeakViewerTemplateHandler {
    public function showChannels($channels) {
        return WCF::getTPL()->fetch('__teamSpeakViewerChildChannels', 'wcf', ['__childChannels' => $channels]);
    }
}