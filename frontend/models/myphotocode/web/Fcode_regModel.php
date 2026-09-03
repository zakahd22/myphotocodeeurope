<?php
require_once G_PATH . "models/baseModel.php";

class Fcode_regModel extends baseModel{
//    public $event;
//    public $events_array;

    public function __construct() {
        parent::__construct();
    }
    public function getFcode($idDongle){
        $this->setFilter('idDongle', '=', $idDongle);
        $this->setOrder('dateEnd');
        
        $query =  $this->select('Fcode_reg');
        
        return $query;
    }
    
     public function getDateEndFcodeReg($idDongle){
        $this->setFilter('idDongle', '=', $idDongle);
        $this->setOrder('dateEnd', 'DESC');
        $this->setLimit(1);
        
        $query =  $this->select('Fcode_reg');
        
        return $query;
    }
    
    public function getDateEndFcode($idDongle, $date){
        $this->setFilter('idDongle', '=', $idDongle);
        $this->setFilter('dateEnd', '>=', $date, 'AND');
        $this->setOrder('dateEnd', 'ASC');
        $this->setLimit(1);
        
        $query =  $this->select('Fcode_reg');
        
        return $query;
    }
    public function insertFcodeReg(){
        $query = $this->insert('Fcode_reg');
        
        return $query;
    }
    public function delFcodeReg($id){
        $this->setFilter("id", "=", $id);
        
        $query = $this->delete('Fcode_reg');
        
        return $query;
    }
    
    public function updateFcodeReg($id, $updates){
        $this->setFilter("id", "=", $id);
        
        $query = $this->update('Fcode_reg', $updates);
        
        return $query;
    }
    
}
