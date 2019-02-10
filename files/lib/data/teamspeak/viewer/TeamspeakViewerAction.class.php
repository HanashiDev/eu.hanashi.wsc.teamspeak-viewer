<?php
namespace wcf\data\teamspeak\viewer;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\builder\TeamSpeakViewerClientBuilder;
use wcf\system\cache\builder\TeamSpeakViewerChannelBuilder;
use wcf\system\cache\builder\TeamSpeakViewerServerBuilder;
use wcf\system\WCF;

class TeamspeakViewerAction extends AbstractDatabaseObjectAction {
    /**
	 * @inheritDoc
	 */
    protected $allowGuestAccess = ['showData'];

    public function validateShowData() {}

    public function showData() {
        $data = $this->parameters['data'];
        $type = $this->parameters['data']['type'];
        $id = $this->parameters['data']['id'];
        
        if ($type == 'client') {
            $data = TeamSpeakViewerClientBuilder::getInstance()->getData([$id]);
            if (count($data) == 0) return ['type' => 'unknown'];
            return [
                'type' => 'client',
                'data' => $data
            ];
        } else if ($type == 'channel') {
            $data = TeamSpeakViewerChannelBuilder::getInstance()->getData([$id]);
            if (count($data) == 0) return ['type' => 'unknown'];
            return [
                'type' => 'channel',
                'data' => $data
            ];
        } else if ($type == 'server') {
            $data = TeamSpeakViewerServerBuilder::getInstance()->getData();
            if (count($data) == 0) return ['type' => 'unknown'];
            return [
                'type' => 'server',
                'data' => $data
            ];
        }

        return [];
    }
}