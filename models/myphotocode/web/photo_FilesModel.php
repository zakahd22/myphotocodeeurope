<?php
require_once G_PATH . "models/baseModel.php";

class photo_FilesModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllPhotoFilesScript($limit = false){
        $this->setFilter('photobooth', 'IS', 'NULL');
        $this->setFilter('dongle', 'IS', 'NULL', 'OR');
        $this->setFilter('ServerId', 'IS', 'NULL', 'OR');
        if($limit){
            $this->setLimit($limit);
        }
        return $this->select('photo_Files');
    }   
    
    public function getAllErrorPathScript($path, $limit = false){
        $this->setFilter('path', 'LIKE', $path.'%');
        if($limit){
            $this->setLimit($limit);
        }
        return $this->select('photo_Files');
    } 
    
    public function getAllErrorPhotoIdScript($limit = false){
        $this->setFilter('photoId', 'IS', 'NULL');
        if($limit){
            $this->setLimit($limit);
        }
        $this->setOrder('id', 'DESC');
        return $this->select('photo_Files');
    } 
    
    public function getAllErrorDatesScript($data = false, $limit = false){
        if($data){
            $this->setFilter('date', '>', $data);
            if($limit){
                $this->setLimit($limit);
            }
            $this->setOrder('date', 'DESC');
            return $this->select('photo_Files');
        }
        else {
            return false;
        }
    } 
    
    public function getphoto_Files($id = false){
        if($id){$this->setFilter("idBooth", "=", $id);}
        return $this->select('photo_Files');
    }   
    
    public function getphoto_File($id){
        $result = FALSE;
        if($id){
            $this->setFilter("idBooth", "=", $id);
            $result = $this->select('photo_Files');
        }
        return $result;
    }   
    
    public function updatePhoto_Files($id, $updates){
        $this->setFilter('id', '=', $id);
        return $this->update('photo_Files', $updates);
    }
    
    public function updatePhoto_FilesByPath($path, $updates){
        $this->setFilter('path', '=', $path);
        return $this->update('photo_Files', $updates);
    }
    //eliminar es per fer test
    public function updatePhoto_FilesByPath_debug($path, $updates){
        $this->setFilter('path', '=', $path);
        return $this->update_debug('photo_Files', $updates);
    }
    
    public function updatePhoto_Files_CodePhoto($code, $updates){
        if($code){
            $this->setFilter('name', 'LIKE', $code."%");
            return $this->update('photo_Files', $updates);
        }
        else {
            return false;
        }
    }

    public function updatePhoto_Files_FilePhoto($file, $updates){
        $this->setFilter('name', 'LIKE', $file);
        return $this->update('photo_Files', $updates);
    }
    
    public function insertphoto_Files(){
        return $this->insert('photo_Files');
    }
    
    public function getFile($file){
        $this->setFilter('name', 'LIKE', $file);
        return $this->select('photo_Files');
    }
    
    public function getPhotoFilesScreens($limit){
        $this->setFilter('name', 'LIKE', '%-S%');
        $this->setFilter('path', 'NOT LIKE', '%.zip%', 'AND');
        $this->setFilter('ServerId', '=', '1', 'AND');
        $this->setOrder("date");
        $this->setLimit($limit);
        
        return $this->select('photo_Files');
    }
    
    public function getFilesNotMirrored($limit){
        $this->setFilter('mirrored', '=', '0');
        $this->setFilter('ServerId', '=', '1', 'AND');
        $this->setFilter('path', 'NOT LIKE', '%_compressed.zip', 'AND');
        
        $this->setOrder("date");
        $this->setGroup('path');
        $this->setLimit($limit);
        
        return $this->select('photo_Files');
    }
    
    public function getPhotoFilesScreensCounter(){
        $this->setFilter('name', 'LIKE', '%-S%');
        return $this->select('photo_Files', 'count');
    }
    
    public function delPhotoFile($id){
        $this->setFilter('id', '=', $id);
        utils::log("This $id does not exist deleting from DB", "logDelPhotoFile");
        return $this->delete('photo_Files');
    }
    
    /*
     * STATISTICS FUNCTIONS
     */
    public function getStatisticReportInfo($date1, $date2, $type){
        $this->setBetweenFilter('date', $date1, 'AND', $date2);
        $this->setFilter("name", "LIKE", "%$type%");
        return $this->select('photo_Files', 'count');
    }
}


    
