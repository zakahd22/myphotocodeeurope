<?php
require_once G_PATH . "models/baseModel.php";

class CLD_DistributorsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the EstadistiquesPhoto from the $photo_id
     */
    
    public function getDistributor($id){
        $this->setFilter("id", "=", $id);
        return $this->select('CLD_Distributors', 'name');
    }
    
    /**
     * Get the EstadistiquesPhoto from estadistiquesPhotos
     */
    public function getDistributors(){
        return $this->select('CLD_Distributors');
    }
    
    /**
     * Get the Name from CLD_Distributors
     */
    public function getDistributorName($id){
        $this->setFilter('id', '=' , $id);
        return $this->select('CLD_Distributors');
    }
}
