<?php
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "models/baseModel.php";
        
class backupController extends baseController{
    public  $lang;
    public  $order_events_array;

    public function __construct() {
        parent::__construct();        
    }
    
    public function getDB_backup(){
        $backupFile = array();
        if(date("N")==2){ //dimecres guardem estadistiques, copia setmanal només
            array_push($backupFile, $this->createBackupFile('myphotocode_statistics'));
           
        }else{//La resta de dies fem copies de web
            array_push($backupFile, $this->createBackupFile('myphotocode_web'));
        }
        
        //array_push($backupFile, $this->createBackupFile('myphotocode_web'));
       // array_push($backupFile, $this->createBackupFile('myphotocode_web'));
       // array_push($backupFile, $this->createBackupFile('myphotocode_trashed'));
       // array_push($backupFile, $this->createBackupFile('myphotocode_statistics'));
       // array_push($backupFile, $this->createBackupFile('apns_owner'));
       // array_push($backupFile, $this->createBackupFile('apns_user'));
        
        $json = array();
        $i = 0;
        foreach($backupFile as $file){
            if(file_exists(G_PATH . $file)){
                $json[$i] =  $file;
                $i++;
            }
        }
        
        return $json;
    }
    
    public function removeDB_backup(){
        $response = array(0, "Not all files deleted");
        
        if(isset($_REQUEST['p1'])){
            $backupFiles = $_REQUEST['p1'];  
        }
        
        $files = json_decode($backupFiles);
        $i = 0;
        foreach ($files as $file){
            if(file_exists(G_PATH . $file)){
                unlink(G_PATH . $file);
                $i++;
            }
        }
        
        if($i == count($files)){
            $response = array(1, "Success from myphotocode!");
        }
        
        return $response;
    }
    
    public function getFiles_mirroring(){
        $this->createModel('photo_Files');

        $filesNotMirrored = $this->photo_FilesModel->getFilesNotMirrored(100);
        
        $json = array();
        $error = array();
        $i = 0;
        $j = 0;
      
        foreach($filesNotMirrored as $file){
            if(file_exists(G_PATH . $file["path"])){
                $json[$i][0] =  $file["id"];
                $json[$i][1] =  $file["path"];
                $i++;
            } else {
                $error[$j][0] =  $file["id"];
                $error[$j][1] =  $file["path"];
                $j++;
            }
        }

        $this->setUnmirrorable_files($error);        
        
        return $json;
    }
    
    public function setSuccess_mirroring(){
        $result = 0;
        $message = "Not all files set as Mirrored!";
        
        $this->createModel('photo_Files');
        
         if(isset($_REQUEST['p1'])){
            $successMirrored = $_REQUEST['p1'];  
        }
    
        $files = json_decode($successMirrored);

        $i=0;
        foreach($files as $file){
            $updates = array('mirrored' => 1);
            $success = $this->photo_FilesModel->updatePhoto_FilesByPath($file[1], $updates);
            if($success) $i++;
        }
        
        if($i == count($files)){
            $result = true;
            $message = "Success from Myphotocode!";
        }
        
        return array($result, $message);
    }
    
    public function setAsUnmirrorable_mirroring(){
        $result = 0;
        $message = "Not all files set as Unmirrorable!";
        
        $this->createModel('photo_Files');
        
         if(isset($_REQUEST['p1'])){
            $unsuccessMirrored = $_REQUEST['p1'];  
        }
        $files = json_decode($unsuccessMirrored);
        
        $i = $this->setUnmirrorable_files_from_p1($files);
        
        if($i == count($files)){
            $result = true;
            $message = array("Success from Myphotocode!");
        }
        
        return array($result, $message);
    }
    
    private function setUnmirrorable_files($files){
        $i=0;
        foreach($files as $file){
            if(!file_exists(G_PATH . $file[1])){
                $updates = array('mirrored' => -1);
                
                $success = $this->photo_FilesModel->updatePhoto_FilesByPath_debug($file[1], $updates);
                if($success) $i++;
            }
        }
        
        return $i;
    }
    
    private function setUnmirrorable_files_from_p1($files){
        $i=0;
        foreach($files as $file){
            print G_PATH . $file;
                
            if(!file_exists(G_PATH . $file)){
                $updates = array('mirrored' => -1);
                print "dins";
                print_r($file);exit;
                $success = $this->photo_FilesModel->updatePhoto_FilesByPath_debug($file, $updates);
                if($success) $i++;
            }
        }
        
        return $i;
    }
    
    private function createBackupFile($id_db){
        $baseModel = new baseModel($id_db);
        return $baseModel->createDBbackup();
    }
}