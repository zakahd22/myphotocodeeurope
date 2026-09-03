<?php
require_once G_PATH . "models/baseModel.php";

class App_boothConfigDefModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from the ?
     */
    public function getApp_boothConfigDef($id, $type = false){
        $this->setFilter('idBooth', '=', $id);
        if($type){
            $this->setFilter("typeConfig", "=", $type, "AND");
        }
        return $this->select('App_boothConfigDef');
    }
    
    public function insertAppBoothConfigDed(){
        return $this->insert('App_boothConfigDef');
    }
    
     public function updAppBoothConfigDef($id, $array, $type){
        $this->setFilter('idBooth', '=', $id);
        if($type){
            $this->setFilter("typeConfig", "=", $type, "AND");
        }
        return $this->update('App_boothConfigDef', $array);
    }
}


