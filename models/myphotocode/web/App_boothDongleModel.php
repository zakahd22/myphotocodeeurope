<?php
require_once G_PATH . "models/baseModel.php";

class App_boothDongleModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from the ?
     */
    
    public function boothDongle($booth_id){
        $this->setFilter('idBooth', '=', $booth_id);
        return $this->select('App_boothDongle');
    }
    
    public function getPbsDongle($dongle_id){
        $this->setFilter('idDongle', '=', $dongle_id);
        $this->setGroup("idBooth");
        
        $query = $this->select('App_boothDongle');
        
        return $query;
    }
    
    /**
     * idDongle
     * order by datetimeS
     * @param type $booth_id
     * @return type
     */
    public function boothPhotobooth($idDongle, $limit = FALSE){
        $result = FALSE;
        
        $this->setFilter('idDongle', '=', $idDongle);
        $this->setFilter("datetimeF", "IS", "NULL", "AND");
        if($limit){$this->setLimit($limit);}
        $this->setOrder('datetimeS', 'DESC');
        
        $result = $this->select('App_boothDongle');
//        utils::log($this->get_sql_string(), 'logAleix');
        return $result;
    }
    
    public function boothDongleLmit($booth_id, $limit = FALSE){
        $this->setFilter('idBooth', '=', $booth_id);
        $this->setFilter("datetimeF", "IS", "NULL", "AND");
        if($limit){$this->setLimit($limit);}
        return $this->select('App_boothDongle');
    }
    
    
    public function getBoothDongle($dongleID){
        $this->entity->loadEntity('App_boothDongle');
        $fields .= $this->entity->getEntityFields("idBooth");
        $fields .= ', '; 
        $this->entity->loadEntity('App_booths');
        $fields .= $this->entity->getEntityFields("boothDongle");
        $sql = "SELECT {$fields}
                FROM App_boothDongle
                LEFT JOIN App_booths 
                ON App_boothDongle.idBooth=App_booths.idBooth";
        if($dongleID){
            $this->setFilter("App_boothDongle.idDongle", "=", $dongleID);
        }
        $this->setOrder("App_boothDongle`.`datetimeS", "DESC");
        $this->setLimit(1);
        $query = $this->my_query($sql);
        return $this->requestQueryResults($query, array("idBooth","boothDongle"), array("App_boothDongle","App_booths"));
    }
    
    public function boothIdDongleLimit($idDongle, $date3, $limit = FALSE){
    //SELECT idBooth FROM App_boothDongle
    //WHERE idDongle=$idDongle AND datetimeS < '$date3' AND (datetimeF IS NULL OR datetimeF > '$date3') LIMIT 1
        $this->setFilter('idDongle', '=', $idDongle);
        $this->setFilter("datetimeS", "<", $date3, "AND");
        $this->setFilter("datetimeS", "IS", "NULL", "AND");
        $this->setFilter("datetimeF", ">", $date3, "OR");
        $this->setOrder('datetimeS', 'DESC');
        if($limit){
            $this->setLimit($limit);
        }
        return $this->select('App_boothDongle');
    }
    
    
    
    /**
     * Get the boothDongle from App_booth
     */
    public function boothDongles($pbs_id, $order = FALSE){
        if($order !== FALSE){$this->setOrder('datetimeS', 'DESC');}
        $this->setFilter('idBooth', '=', $pbs_id);
        return $this->select('App_boothDongle');
    }
    
    
    
    public function boothDonglescript($limit = FALSE){
        $this->setLimitAndNumber($limit, 200);
        return $this->select('App_boothDongle');
    }
    
    
    public function updPairingsNotFinish($idPb, $updates){
        $this->setFilter('idBooth', '=', $idPb);
        $this->setFilter("datetimeF", "IS", "NULL", "AND");
        
        return $this->update('App_boothDongle', $updates);
    }
    
    public function insertPairing(){
        return $this->insert('App_boothDongle');
    }
}


