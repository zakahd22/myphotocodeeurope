<?php
require_once G_PATH . "models/baseModel.php";

class ftp_foldersModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllftp_folders(){    
        return $this->select('ftp_folders');
    }
    
    public function getftp_folder($ftp_folder_id){
        $this->setFilter("id", "=", $ftp_folder_id);
        return $this->select('ftp_folders');
    }
    
    public function getftp_folderRand(){
        $this->setOrder('RAND()');
        $this->setLimit(1);
        return $this->select('ftp_folders');
    }
    
    public function updateFtp_folder($id, $updates){
        $this->setFilter('id', '=', $id);
        return $this->update('events', $updates);
    }
}


