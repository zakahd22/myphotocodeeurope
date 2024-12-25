<?php
require_once G_PATH . "models/baseModel.php";

class App_bootDCAllowedModel extends baseModel{
//    public $event;
//    public $events_array;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getApp_bootDCAllowed(){
        return $this->select('App_bootDCAllowed');
    }
    
    public function updateApp_bootDCAllowed($idbDCAll, $updates){
        $this->setFilter('id', '=', $idbDCAll);      
        return $this->update('App_bootDCAllowed', $updates);
    }
    
}
