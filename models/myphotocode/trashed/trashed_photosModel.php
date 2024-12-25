<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_photosModel extends trashedModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * LookPhoto
     * get a single photo
     * 
     * @param type $code
     * @return Array Containing an array with the entity photos
     */
    public function getPhoto($code=false){
        if($code){
            $this->setFilter('code', 'LIKE', $code);
        }
        return $this->select('trashed_photos');
    }
    
    /*
     * STATISTICS FUNCTIONS
     */
    public function getStatisticReportInfo($date1, $date2){
        $this->setBetweenFilter('Appusr_datetime', $date1, 'AND', $date2);
        return $this->select('trashed_photos', 'count');
    }
    
}