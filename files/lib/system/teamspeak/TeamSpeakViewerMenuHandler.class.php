<?php

namespace wcf\system\teamspeak;

use wcf\system\cache\builder\TeamSpeakViewerMenuBuilder;
use wcf\system\SingletonFactory;

class TeamSpeakViewerMenuHandler extends SingletonFactory
{
    public function getClientlist()
    {
        return TeamSpeakViewerMenuBuilder::getInstance()->getData();
    }

    public function getClientCount()
    {
        return count($this->getClientlist());
    }
}
