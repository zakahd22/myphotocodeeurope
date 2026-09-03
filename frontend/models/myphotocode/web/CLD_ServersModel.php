<?php
require_once G_PATH . "models/baseModel.php";

class CLD_ServersModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCLD_Servers($name){
        $this->setFilter("name", "LIKE", $name);
        return $this->select('CLD_Servers');
    }   
    
    public function insertCLD_Servers(){
        return $this->insert('CLD_Servers');
    }
}