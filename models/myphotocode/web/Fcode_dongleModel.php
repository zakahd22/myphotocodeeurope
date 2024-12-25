<?php
require_once G_PATH . "models/baseModel.php";

class Fcode_dongleModel extends baseModel{
//    public $event;
//    public $events_array;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getFinancingDongle(){
        return $this->select('Fcode_dongle');
    }
    
    public function getFinancingDongleById($dongleId){
        $this->setFilter('idDongle', '=', $dongleId);
        
        $query = $this->select('Fcode_dongle');
        
        return $query;
    }
        public function insertFcodeReg(){
        $query = $this->insert('Fcode_dongle');
        
        return $query;
    }
    
    public function updateFinancingDongle($id, $updates){
        $this->setFilter("idDongle", "=", $id);
        
        $query = $this->update('Fcode_dongle', $updates);
        
        return $query;
    }
    
}
