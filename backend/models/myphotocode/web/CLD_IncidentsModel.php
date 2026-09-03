<?php
require_once G_PATH . "models/baseModel.php";

class CLD_IncidentsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the Incident from the ?
     */
    
    public function getIncident($pb_id){
        
        return $this->select('CLD_Incidents');
    }
    
       /**
     * Get the boothDongle from App_booth
     */
    public function getAllIncident($idBooth=false){
        if($idBooth){
            if($idBooth != '1=0'){
                $this->setFilter("idBooth", "=", "$idBooth");
            }
            else {
                $this->setFilter("1", "=", "0");
            }
        }
        $this->setOrder("status`, `datetime", "DESC");
        return $this->select('CLD_Incidents');
    }
    
    /**
     * Get the Incidents from CLD_Incidents
     */
    public function getIncidents($pb_id){
        $this->setFilter('idBooth', '=', $pb_id);
        $this->setFilter('status', '<', 2, "AND");
        return $this->select('CLD_Incidents', 'count');
    }
    
    public function getIncidentsBooth($pb_id){
        $this->setFilter('idBooth', '=', $pb_id);
        return $this->select('CLD_Incidents');
    }
    
    public function getIncidentsByBooth($pb_id){
        $this->setFilter('idBooth', '=', $pb_id);
        $this->setFilter('status', '<', 2, "AND");
        return $this->select('CLD_Incidents');
    }
    
    public function insertIncidents(){
        return $this->insert('CLD_Incidents');
    }
    
    
    
    
//S'ha comentat pq no es cridava des de cap lloc i el nom era adequat per un altre funció
//    public function getIncidentsBooth($arrayBooths,  $typeAlert, $operatorEstat){
//        $this->setInFilter("App_boothAlert.idBooth", $arrayBooths);
//        $this->setFilter("App_boothAlert.estat", $operatorEstat, "2", "AND");
//        $this->setInFilter("typeAlert", $typeAlert);
//        $this->setOrder("App_boothAlert`.`when", "DESC");
//
//        $this->entity->loadEntity('App_boothAlert');
//        $fields .= $this->entity->getEntityFields();  
//        $fields .= ', ';  
//        $this->entity->loadEntity('App_booths');
//        $fields .= $this->entity->getEntityFields();
//        
//        $sql = "
//            SELECT {$fields}
//            FROM App_boothAlert
//            LEFT JOIN App_booths
//            ON App_booths.idBooth = App_boothAlert.idBooth
//        ";
//        
//        $query = $this->my_query($sql);
//        
//        $result = $this->requestQueryResults($query, 'all', array('App_boothAlert','App_booths'));
//        
//        return $result;
//    }
}
