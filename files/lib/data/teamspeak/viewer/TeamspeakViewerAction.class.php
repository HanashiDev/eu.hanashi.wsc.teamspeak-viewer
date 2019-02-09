<?php
namespace wcf\data\teamspeak\viewer;
use wcf\data\AbstractDatabaseObjectAction;
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
        
        return [];
    }
}