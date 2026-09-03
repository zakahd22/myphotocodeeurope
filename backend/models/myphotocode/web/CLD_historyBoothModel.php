<?php
require_once G_PATH . "models/baseModel.php";

class CLD_historyBoothModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    

    public function geHistoryBooth($id){
        $this->setFilter("idBooth", "=", $id);
        $this->setOrder("data", "DESC");
        return $this->select('CLD_historyBooth');
    }   
    
    public function insertCLD_historyBooth(){
        return $this->insert('CLD_historyBooth');
    }
}


