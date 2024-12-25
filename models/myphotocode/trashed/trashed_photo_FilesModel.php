<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_photo_FilesModel extends trashedModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getphoto_Files($id = false){
        if($id){
            $this->setFilter("idBooth", "=", $id);
        }
        return $this->select('trashed_photo_Files');
    }   
    
//    public function updatePhoto_Files($id, $updates){
//        $this->setFilter('id', '=', $id);
//        return $this->update('photo_Files', $updates);
//    }
//    
//    public function insertphoto_Files(){
//        return $this->insert('photo_Files');
//    }
}


