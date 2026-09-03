<?php
require_once G_PATH . "models/baseModel.php";

class CLD_boothTypesModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the EstadistiquesPhoto from the $photo_id
     */
    public function getBoothTypesModel___(){
        $this->setFilter('photo', '=', $photo);
        return $this->select('CLD_boothTypes');
    }
    
    /**
     * Get the Photobooths Models from CLD_boothTypesModel
     */
    public function getBoothTypesModels(){
        $this->setOrder("CLD_modelSN");                        
        return $this->select('CLD_boothTypes');
    }
    
        
    public function getBoothsIdIn($array, $char = false){
        $this->setInFilter('id', $array);
        return $this->select('CLD_boothTypes');
    }
    
     /**
     * Get the Photobooths Models from CLD_boothTypesModel
     */
    public function getBoothTypesModelsIncidents($filter, $idType, $boothChar){
        if($filter){
            $this->setFilter('id', '=', $idType);
        }
        else{
            $this->setFilter('`char`', '=', $boothChar);
            $this->setOrder("id");
            $this->setLimit(1);
                    
        }

        return $this->select('CLD_boothTypes');
    }
    
    public function getBoothTypeName($type_id){
        $this->setFilter("id", "=", $type_id);                        
        return $this->select('CLD_boothTypes', 'name');   
    }
    
    public function getBoothTypeByChar($char){
        $this->setFilter("`char`", "=", $char); 
        $this->setLimit(1);
        return $this->select('CLD_boothTypes', 'name');
    }
    
    public function getBoothTypeByModel($type){
        $this->setFilter("CLD_modelSN", "=", $type);
        return $this->select('CLD_boothTypes');
    }
}