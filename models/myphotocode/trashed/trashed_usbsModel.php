<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_usbsModel extends trashedModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * idBooth = $id
     * @return type
     */
    public function get_usbs(){
        return $this->select('trashed_usbs');
    }   

}