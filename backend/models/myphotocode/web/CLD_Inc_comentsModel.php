<?php
require_once G_PATH . "models/baseModel.php";

class CLD_Inc_comentsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     */
    
    public function Inc_coment($id){
        $this->setFilter("incident", "=", $id);
        return $this->select('CLD_Inc_coments');
    }
    
    /**
     * 
     */
    public function getDistributors(){
        return $this->select('CLD_Inc_coments');
    }
}
