<?php
require_once G_PATH . "models/baseModel.php";

class App_boothsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from the ?
     */
    public function getBooths($owner, $order = false){
        $this->setFilter('owner', '=', $owner);
        if($order){
            $this->setGroup($order);
        }
        return $this->select('App_booths');
    }
    
    /**
     * Get the boothDongle from the ?
     */
    public function getBoothsLimit($owner, $limit, $order = false){
        $this->setFilter('owner', '=', $owner);
        if($order){
            $this->setGroup($order);
        }
        $this->setLimit($limit);
        
        $data = $this->select('App_booths');
        
//        utils::log($this->get_sql_string(), logMoment);
        
        return $data;
        
    }
    
    /**
     * 
     * @param type $owner
     * @return type
     */
    public function getBoothsFiltered($owner){
        $this->setFilter('owner', '=', $owner);
        $this->setFilter('CLD_idType', 'IS NOT', 'NULL');
        $this->setOrder('CLD_idType');
        
        return $this->select('App_booths');
    }
    
    /**
     * Get the boothDongle from App_booth
     */
    public function getBooth($serialnumber){
        $this->setFilter('serialnumber', '=', $serialnumber);
        return $this->select('App_booths');
    }
    
    /**
     * Get the boothDongle from App_booth
     */
    public function getBoothID($id_booth){
        $this->setFilter('idBooth', '=', $id_booth);
        return $this->select('App_booths');
    }
    
    public function getBoothWhereid($idBooth){
        $this->setFilter('idBooth', '=', $idBooth);
        return $this->select('App_booths');
    }
    
    public function getDistributorsPbs($CLD_Distributors){
        $this->setFilter('CLD_Distributor', '=', $CLD_Distributors);
        $this->setGroup("owner");
        return $this->select('App_booths',"distributor");
    }
    
    public function getBoothAndBoothAlerts($arrayBooths,  $typeAlert, $operatorEstat){
        $this->setInFilter("App_boothAlert.idBooth", $arrayBooths);
        $this->setFilter("App_boothAlert.estat", $operatorEstat, "2", "AND");
        $this->setInFilter("typeAlert", $typeAlert);
        $this->setOrder("App_boothAlert`.`when", "DESC");

        $this->entity->loadEntity('App_boothAlert');
        $fields .= $this->entity->getEntityFields();  
        $fields .= ', ';  
        $this->entity->loadEntity('App_booths');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields}
            FROM App_boothAlert
            LEFT JOIN App_booths
            ON App_booths.idBooth = App_boothAlert.idBooth
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all', array('App_boothAlert','App_booths'));
        
        return $result;
    }
    
    public function getAllPbs($limit = false){
        $this->setOrder("serialnumber", "DESC");
        
        if($limit){
            $this->setLimit ($limit);
            $request = $this->select('App_booths');
        }
        else{
            $request = $this->select('App_booths', "count");
        }
        return $request;
    }
    
    public function getPbsListFilter($serialnumber=false, $type=false, $status=false, $idbooth=false, $distributor=false, $owner=false){
     //   utils::log("owner: {$owner}", logMoment);
        if($serialnumber){
            $this->setFilter("serialnumber", "LIKE", "%".$serialnumber."%");
        }
        if($type){
            $this->setFilter("CLD_idType", "=", $type, "AND");
        }
        if($status){
            $this->setFilter("CLD_Status", "=", $status, "AND");
        }
        if($idbooth){
            $this->setFilter("idBooth", "=", $idbooth, "AND");
        }
        
        if($distributor){
            $this->setFilter("CLD_Distributor", "=", $distributor, "AND");
        }
        if($owner){
            $this->setInFilter("owner", $owner, "AND");
        }
        
        $result = $this->select('App_booths');
     //   utils::log($this->get_sql_string(), logMoment);
        
        return $result;
        
    }
    
     public function getPbsListFilterWithUPGRADEid($serialnumber=false, $type=false, $status=false, $idbooth=false, $distributor=false, $owner=false, $UPGRADEid=false){
        
        $filterString =" ";
        
        if($serialnumber){
            $filterString .= " AND serialnumber LIKE '%".$serialnumber."%' ";            
        }        
        if($type){
            $filterString .= " AND CLD_idType = '".$type."' ";          
        }
        if($status){
             $filterString .= " AND CLD_Status = '".$status."' ";            
        }
        if($idbooth){
            $filterString .= " AND idBooth = '".$idbooth."' ";            
        }        
        if($distributor){
            $filterString .= " AND CLD_Distributor = '".$distributor."' ";            
        }
        if($owner){
            $filterString .= " AND owner = '".$owner."' ";               
        }
        if($UPGRADEid){
                /**
                 * Afegim ids que estan en aquest UPGRADEid
                 * 
                 */
                $sql = "SELECT * FROM `App_boothBootDC` WHERE UPGRADEid='$UPGRADEid'  GROUP BY idBooth ORDER BY `App_boothBootDC`.`datetimeUpgCheck` DESC";

                $query = $this->my_query($sql);

               if($query){
                    $idBoothInUpg = "";
                    $iComa = 0;
                    while($info = $this->my_fetch_array($query)){
                        if($iComa){
                            $idBoothInUpg     .= ",";
                        }
                        $idBoothInUpg     .=  $info['idBooth'];                        
                        $iComa++;

                    }
    
                }
                else{
                    return "Error Database Repdc_activity, error code: 0002 $sql";
                }
                $filterString .= "AND idBooth IN ($idBoothInUpg) ";         
        }
        
        

        $sql = "
            SELECT *
            FROM App_booths
            
            WHERE  1=1 
            ".$filterString;
      //  utils::log("Consulta amb UPGRADEids".$sql, logMoment);
        $query = $this->my_query($sql);
        
       if($query){
            $result = array();
            
            while($info = $this->my_fetch_array($query)){
                
                $result[$info['idBooth']]['name']     =  $info['name'];
                $result[$info['idBooth']]['owner']    =  $info['owner'];
                $result[$info['idBooth']]['idBooth']  =  $info['idBooth'];
                $result[$info['idBooth']]['serialnumber']   =  $info['serialnumber'];
                $result[$info['idBooth']]['rand_string']   =  $info['rand_string'];
                $result[$info['idBooth']]['type']   =  $info['type'];
                $result[$info['idBooth']]['CLD_idType']   =  $info['CLD_idType'];                
                $result[$info['idBooth']]['location']   =  $info['location'];
                $result[$info['idBooth']]['CLD_Status']   =  $info['CLD_Status'];
                $result[$info['idBooth']]['CLD_Distributor']   =  $info['CLD_Distributor'];
               
        
                
            }
//           $result = $this->my_fetch_array($query);
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        
        return $result; 
        
    }
    
    public function getPbsListFilterAuditsSuper($serialnumber=false, $type=false, $status=false, $idbooth=false, $distributor=false, $owner=false){
        
        if($serialnumber){
            $this->setFilter("serialnumber", "LIKE", "%".$serialnumber."%");
        }
        if($type){
            $this->setFilter("CLD_idType", "=", $type, "AND");
        }
        if($status){
            $this->setFilter("CLD_Status", "=", $status, "AND");
        }
        if($idbooth){
            $this->setFilter("idBooth", "=", $idbooth, "AND");
        }
        
        if($distributor){
            $this->setFilter("CLD_Distributor", "=", $distributor, "AND");
        }
        if($owner){
            $this->setFilter("owner", "=", $owner, "AND");
        }
        
        $result = $this->select('App_booths');
        
        return $result;
        
    }
    
     public function getPbsListFilterAuditsSuperWithDongle($serialnumber=false, $type=false, $status=false, $idbooth=false, $distributor=false, $owner=false){
        $filterString =" ";
        if($serialnumber){
            $filterString .= " AND serialnumber LIKE '%".$serialnumber."%' ";            
        }        
        if($type){
            $filterString .= " AND CLD_idType = '".$type."' ";          
        }
        if($status){
             $filterString .= " AND CLD_Status = '".$status."' ";            
        }
        if($idbooth){
            $filterString .= " AND idBooth = '".$idbooth."' ";            
        }        
        if($distributor){
            $filterString .= " AND CLD_Distributor = '".$distributor."' ";            
        }
        if($owner){
            $filterString .= " AND owner = '".$owner."' ";               
        }
//AND App_boothDongle.datetimeF IS NULL
        $sql = "
            SELECT App_booths.name, App_booths.owner, App_booths.idBooth, App_booths.serialnumber, name, booths.rand_string
            FROM App_booths
            LEFT JOIN App_boothDongle ON App_booths.idBooth = App_boothDongle.idBooth
            LEFT JOIN booths ON booths.id = App_boothDongle.idDongle 
            WHERE  1=1 
            ".$filterString."
            GROUP BY idBooth
            ORDER BY datetimeS DESC";
        
        $query = $this->my_query($sql);
        
       if($query){
            $result = array();
            
            while($info = $this->my_fetch_array($query)){
                
                $result[$info['idBooth']]['name']     =  $info['name'];
                $result[$info['idBooth']]['owner']    =  $info['owner'];
                $result[$info['idBooth']]['idBooth']  =  $info['idBooth'];
                $result[$info['idBooth']]['serialnumber']   =  $info['serialnumber'];
                $result[$info['idBooth']]['rand_string']   =  $info['rand_string'];
                
            }
//           $result = $this->my_fetch_array($query);
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        
        return $result; 
             
        
         
       
        
      
        
        
    }
    
    
   
    
    public function getPbsListFilterManufacturer($serialnumber=false, $type=false, $status=false, $distributor=false, $owner=false, $arrayPbsIds=false,$idPb=false){
        $filter = FALSE;
        $result = FALSE;
        
        if($serialnumber){
            $this->setFilter("serialnumber", "LIKE", "%".$serialnumber."%");
            $filter = TRUE;
        }
        if($type){
            $this->setFilter("CLD_idType", "=", $type, "AND");
            $filter = TRUE;
        }
        if($status){
            $this->setFilter("CLD_Status", "=", $status, "AND");
            $filter = TRUE;
        }
        
        if($arrayPbsIds){
            $this->setInFilter("idBooth", $arrayPbsIds);
            $filter = TRUE;
        }
        
        else if($idPb){
            $this->setFilter("idBooth", "=", "$idPb");
            $filter = TRUE;
        }
        
        if($distributor){
            $this->setFilter("CLD_Distributor", "=", $distributor, "AND");
            $filter = TRUE;
        }
        if($owner){
            $this->setInFilter("owner", $owner, "AND");
            $filter = TRUE;
        }
        if($filter){
            $result = $this->select('App_booths');
        }
        
        return $result;
    }
    
    
    
    public function getPbsAsDistributor($id, $limit = FALSE){
        $this->setFilter("CLD_Distributor", "=", $id);
        $this->setFilter("CLD_Status", ">", "1", "AND");
        $this->setOrder("serialnumber");
        
        if($limit){
            $this->setLimit ($limit);
            $result = $this->select('App_booths');
        }
        else {
            $result = $this->select('App_booths', 'count');  
        }
        return $result;      
    }
    
    /**
     * Updates the App_booths where idBooth = $id
     * 
     * @param Int idBooth 
     * @param Array $updates Containing an array with the strings
     */
    public function updateAppBooths($id, $updates){
        $this->setFilter('idBooth', '=', $id);
        return $this->update('App_booths', $updates);
    }
    
    public function getBoothstypefil($owner){
        $this->setFilter('owner', '=', $owner);
        $this->setFilter('CLD_idType', 'IS NOT', 'NULL');
        $this->setOrder('CLD_idType');
        
        $this->setGroup('CLD_boothTypes.name');
        
        $this->entity->loadEntity('CLD_boothTypes');
        $fields .= $this->entity->getEntityFields();  
        $fields .= ', ';  
        $this->entity->loadEntity('App_booths');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields}
            FROM CLD_boothTypes
            LEFT JOIN App_booths
            ON App_booths.CLD_idType = CLD_boothTypes.id
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all', array('CLD_boothTypes','App_booths'));
        
        return $result;
    }
    


    public function getBoothsTypeSegregated($owner, $pbmodel){
        $pbmodel = "%$pbmodel%";
        $this->setFilter('owner', '=', $owner, "AND");
        $this->setFilter('CLD_idType', 'IS NOT', 'NULL', "AND");
        $this->setFilter('App_booths.version', 'LIKE', $pbmodel, "AND");
        $this->setOrder('CLD_idType');
        
        $this->setGroup('CLD_boothTypes.name');
        
        $this->entity->loadEntity('CLD_boothTypes');
        $fields .= $this->entity->getEntityFields();  
        $fields .= ', ';  
        $this->entity->loadEntity('App_booths');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields}
            FROM CLD_boothTypes
            LEFT JOIN App_booths
            ON App_booths.CLD_idType = CLD_boothTypes.id
        ";
        
        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all', array('CLD_boothTypes','App_booths'));
        
        return $result;
    }
    
    public function getBoothNameWhereid($idBooth) {
        $this->setFilter('id', '=', $idBooth);
        return $this->select('CLD_boothTypes');
    }

}

