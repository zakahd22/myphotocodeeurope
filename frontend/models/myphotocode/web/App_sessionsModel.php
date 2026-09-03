<?php

require_once G_PATH . "models/baseModel.php";

class App_sessionsModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
        $this->table = 'App_sessions';
    }
    
    /**
     * Get all App_sessions for a App_booth ordered by date
     */
    public function getApp_sessions($idPB){
        $this->setFilter('idBooth', '=', $idPB);
        $this->setOrder('start');
        
        return $this->select($this->table);
    }
    
    /**
     * Get the first App_sessions for a App_booth
     */
    public function getFirstApp_sessions($idPB){
        $this->setFilter('idBooth', '=', $idPB);
        $this->setOrder('start');
        $this->setLimit(1);

        $result = $this->select($this->table);        
//        utils::log($this->get_sql_string(), 'logAleix');
        
        return $result;
    }
    
    /**
     * Get the first App_sessions for a App_booth
     */
    public function getFirstOwnerApp_sessions($idUser){
        $this->entity->loadEntity($this->table);
        $this->clear_sql_operators();
        
        $query = $this->my_query("
            SELECT `id` , `idBooth` , `idDongle` , MIN( `start` ) , `last`
            FROM {$this->table}
            WHERE idBooth IN (
                SELECT idBooth FROM App_booths
                WHERE owner = {$idUser}
            )
        ");
        
        if($query){
            $result = $this->requestQueryResults($query, 'all');
        }
//        utils::log($this->get_sql_string(), 'logAleix');
        
        return ($query? $result : $query);
    }
    
    /**
     * Get the first App_sessions for a App_booth
     */
    public function getLastApp_sessions($idPB){
        $this->setFilter('idBooth', '=', $idPB);
        $this->setOrder('start', 'DESC');
        $this->setLimit(1);
        
        return $this->select($this->table);
    }
}
