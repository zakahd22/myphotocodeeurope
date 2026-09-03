<?php
require_once G_PATH . "models/baseModel.php";

class boothsModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * get a booth
     * @param 
     * @return Array Containing an array with the entity booth
     */
    public function getBooth($dongle){
        $this->setFilter("dongle", "LIKE", $dongle);
        return $this->select('booths');
    }
    
    public function getAllBooths(){
        return $this->select('booths');
    }
    
    public function getBooths($limit){
        
        $this->setOrder("booths`.`rand_string", "DESC");
        $this->setLimit($limit);
        
        return $this->select('booths');
    }
    
    public function getBoothsByString($string){
        
        $this->setFilter("rand_string", "=", $string);
        
        return $this->select('booths');
    }
    
    public function getBoothsByDongle($dongleID){
        $this->setFilter("id", "=", $dongleID);
        return $this->select('booths');
    }


    public function getDistributorsBooth($CLD_Distributors){
        $this->setFilter('CLD_Distributor', '=', $CLD_Distributors);
        $this->setGroup("rental_id");
        return $this->select('booths',"distributor");
    }
    
    public function getBoothRandString($rand_string){
        $this->entity->loadEntity('App_boothDongle');
        
        $sql = "SELECT App_boothDongle.idBooth FROM booths INNER JOIN App_boothDongle ON booths.id = App_boothDongle.idDongle";
        $this->setFilter("booths.rand_string", "=", $rand_string);
        $this->setFilter("App_boothDongle.datetimeF", "IS", "NULL", "AND");
        $this->setOrder("App_boothDongle`.`datetimeS", "DESC");
        $this->setLimit("1");
        
        $query = $this->my_query($sql);
        
        return $this->requestQueryResults($query, "idBooth");
    }
    
    public function getBoothRandStringManufacturer($rand_string){
        $this->entity->loadEntity('App_boothDongle');
        
        $sql = "SELECT App_boothDongle.idBooth FROM booths INNER JOIN App_boothDongle ON booths.id = App_boothDongle.idDongle";
        $this->setFilter("booths.rand_string", "=", $rand_string);
        $this->setOrder("App_boothDongle`.`datetimeS", "DESC");
        
        $query = $this->my_query($sql);
        
        return $this->requestQueryResults($query, "idBooth");
    }
    
    public function getBoothOwners($rand_string=false, $LIMIT= false){
        $this->entity->loadEntity('booths');
        $fields .= $this->entity->getEntityFields("listDongle");
        $fields .= ', '; 
        $this->entity->loadEntity('rentals');
        $fields .= $this->entity->getEntityFields("listDongle");        
        
        $sql = "SELECT {$fields}
                FROM booths
                LEFT JOIN rentals 
                ON booths.rental_id=rentals.id";
        if($rand_string){
            $this->setFilter("booths.rand_string", "=", $rand_string);
        }
        if($LIMIT){
            $this->setLimit($LIMIT);
        }
        $query = $this->my_query($sql);
        
       
        
        return $this->requestQueryResults($query, array("listDongle","listDongle"), array("booths","rentals"));
    }
    
    
    public function getBoothsOrder(){
        $this->setOrder("rand_string");
        return $this->select('booths');
    }
}


