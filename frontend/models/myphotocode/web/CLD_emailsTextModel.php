<?php
require_once G_PATH . "models/baseModel.php";

class CLD_emailsTextModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    public function getCLD_emailsText($type, $eventId){
        $this->setFilter('type', '=', $type);
        $this->setFilter('event', '=', $eventId, 'AND');
        return $this->select('CLD_emailsText', "text");
    }
    
    
}
