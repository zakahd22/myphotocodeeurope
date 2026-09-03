<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_CLD_estadistiques_photosModel extends trashedModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the EstadistiquesPhoto from the $photo_id
     */
    
    public function getAllstatisticsOnePhoto($photo){
        $this->setFilter('photo', 'LIKE', $photo);
        return $this->select('trashed_CLD_estadistiques_photos');
    }
    
    
    /**
     * 
     * @return type
     */
    public function insertEstaditiquesPhoto(){
        return $this->insert('trashed_CLD_estadistiques_photos');
    }
}
