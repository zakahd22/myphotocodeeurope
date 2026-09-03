<?php
require_once G_PATH . "models/baseModel.php";

class rentalsModel extends baseModel{


    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getRentals($owner){
        $this->setFilter('CLD_DistributorId', '=', $owner);
        return $this->select('rentals');
    }
    
    public function getRentalsNames($rental_id){
        $this->setFilter('id', '=', $rental_id);
        return $this->select('rentals');
    }
    
    public function getRentalsById($rental_id){
        $this->setFilter('id', '=', $rental_id);
        return $this->select('rentals');
    }
    
    public function getAllRentals($dataSet="all", $limit = FALSE){
        $this->setOrder("name");
        
        if($limit){
            $this->setLimit($limit);
        }
        $result = $this->select('rentals', $dataSet);
        return $result;
    }
    
    public function getRental($cname = false, $uName = false){
        if($cname){
            $this->setFilter("name", "like", "%$cname%");
        }
        if($uName){
            $this->setFilter("username", "like", "%$uName%");
        }
        $this->setOrder("name");
//        if($limit)$this->setLimit ($limit);
        //$this->setOrder("name");
        return $this->select('rentals');
    }
//    public function getRental($name, $uName, $limit = FALSE){
//        $this->setFilter("name", "like", "%$name%");
//        $this->setFilter("uname", "like", "%$uName%");
//        if($limit)$this->setLimit ($limit);
//        //$this->setOrder("name");
//        return $this->select('rentals');
//    }
    
    public function getRentalsDistributor($distributor){
        $this->setFilter("CLD_DistributorId", "=", $distributor);
        $this->setGroup("id");
        return $this->select('rentals', 'distributor');
    }
    
    public function getRentalsInfo($array, $limit){
        $this->setInFilter('id', $array);
        if($limit){
            $this->setLimit ($limit);
            $result = $this->select('rentals');
        }
        else {
            $result = $this->select('rentals', 'count');  
        }
        return $result;
    }
    
    public function updateRental($id, $updates){
        $this->setFilter("id", "=", $id);
        return $this->update('rentals', $updates);
    }
    
    public function getRentalsListDistr($USERID, $cName = false, $LIMIT = false){
        $this->entity->loadEntity('rentals');
        $fields .= $this->entity->getEntityFields();
        $sql = "SELECT {$fields}
                FROM rentals
                WHERE (CLD_DistributorId = $USERID
                    OR rentals.id IN (
                        SELECT owner FROM App_booths WHERE CLD_Distributor = {$USERID})
                     )";
        if($cName){
             $sql .= " AND name LIKE '%".$cName."%'";
        }
        if($LIMIT){
            $this->setLimit($LIMIT);
            $this->setOrder("name", "DESC");
        }
        $query = $this->my_query($sql);
        return $this->requestQueryResults($query);
    }
    
    /*
     * "SELECT rentals.App_email FROM rentals LEFT JOIN App_booths ON App_booths.owner=rentals.id "
     */
    public function getOwnersBooth($ID){
        $this->entity->loadEntity('rentals');
        $fields = $this->entity->getEntityFields();      
        //SELECT r.App_email FROM rentals r LEFT JOIN App_booths b ON b.owner=r.id WHERE b.idBooth=
        $sql = "SELECT {$fields}
                FROM rentals
                LEFT JOIN App_booths 
                ON App_booths.owner=rentals.id";
        if($ID){
            $this->setFilter("App_booths.idBooth", "=", $ID);
        }
        $query = $this->my_query($sql);
        return $this->requestQueryResults($query);
    }

//    public function getDistributorRentals($id_user, $limit = false){
//        $this->setFilter("CLD_DistributorId", "=", $id_user);
//        $this->setFilter("id", "IN", "'(SELECT owner FROM App_booths WHERE CLD_Distributor = $id_user)'");
//        $this->setOrder("name");
//        $this->setLimit($limit);
//        
//        return $this->select('rentals');
//    }
    
    
    public function getRentalsListByNameOrUsername($Name = false, $LIMIT = false){
        $this->entity->loadEntity('rentals');
        $fields .= $this->entity->getEntityFields();
        $sql = "SELECT {$fields}
                FROM rentals
                WHERE 1";
        if($Name){
             $sql .= " AND (name LIKE '%".$Name."%' OR username LIKE '%".$Name."%')";
        }
        if($LIMIT){
            $this->setLimit($LIMIT);
            $this->setOrder("name", "DESC");
        }
        $query = $this->my_query($sql);
        return $this->requestQueryResults($query);
    }
    
    
}
    