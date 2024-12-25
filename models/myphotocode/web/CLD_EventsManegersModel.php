<?php
require_once G_PATH . "models/baseModel.php";

class CLD_EventsManegersModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }

    public function getCLD_EventsManegers($id){
        $this->setFilter('id', '=', $id);
        return $this->select('CLD_EventsManegers');
    }
    
    public function updateCLD_EventsManegers($id, $updates){
        $this->setFilter("id", "=", $id);
        return $this->update('CLD_EventsManegers', $updates);
    }
    
    public function insertCLD_EventsManegers(){
        return $this->insert('CLD_EventsManegers');
    }
}
