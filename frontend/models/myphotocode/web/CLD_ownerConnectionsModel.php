<?php
require_once G_PATH . "models/baseModel.php";

class CLD_ownerConnectionsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    
    public function getownerConnection($id, $type_user){
        $this->setFilter("user", "=", $id);    
        $this->setFilter("type_user", "=", $type_user, "AND");    
//20250109owner        $this->setOrder(data, "DESC");
        $this->setOrder('data', "DESC");//20250109owner
        $this->setLimit("5");
        return $this->select('CLD_ownerConnections');
    }
    
    public function getStatisticsReport($date1, $date2){
        $this->setBetweenFilter('data', $date1, 'AND', $date2);
        $this->setFilter("type_user", "=", 4, "AND");   
        $this->setGroup('pais');
        return $this->select('CLD_ownerConnections', 'StatisticsReport');
    }
    
    public function insertCLD_ownerConnections(){
        return $this->insert('CLD_ownerConnections');
    }
    
}

