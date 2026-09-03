<?php
require_once G_PATH . "models/baseModel.php";

class App_boothAlertDefModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    

    public function getAlerts($pbsid, $typeAlert){
        $this->setFilter("idBooth", "=", $pbsid);
        $this->setFilter("typeAlert", "=", $typeAlert, "AND");
        return $this->select('App_boothAlertDef');
    }
    
    public function insertBoothAlertDef(){
        return $this->insert('App_boothAlertDef');
    }
    
    public function updateAlertDef($pbsid, $typeAlert, $updates){
        $this->setFilter("idBooth", "=", $pbsid);
        $this->setFilter("typeAlert", "=", $typeAlert, "AND");
        return $this->update('App_boothAlertDef', $updates);
    }   
}
