<?php
require_once G_PATH . "models/baseModel.php";

class CLD_estadistiques_uploadModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCLD_estadistiques_upload(){
        return $this->select('CLD_estadistiques_upload');
    }   
    
    public function getCLD_estadistiques_uploadDate($date){
        $this->setFilter("date", "=", $date);
        return $this->select('CLD_estadistiques_upload');
    }   
    
    public function insertCLD_estadistiques_upload(){
        return $this->insert('CLD_estadistiques_upload');
    }
    
    public function increase_CLD_estadistiques_upload($date){
        $this->clear_sql_operators();
        
        $sql = "INSERT INTO CLD_estadistiques_upload (date, numUpload)
                VALUES ('$date', 1)
                ON DUPLICATE KEY UPDATE numUpload = numUpload + 1";
        
        return $this->my_query($sql);
    }
}


