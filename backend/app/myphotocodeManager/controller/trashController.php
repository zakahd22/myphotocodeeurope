<?php
require_once G_PATH."common/Classes/baseController.php";
        
class TrashControler extends baseController{
    private $OrdersInfoView;
    private $OrdersModel;
    
    public  $lang;
    public  $order_events_array;
    
    public $time_start;
    public $time_end;

    public function __construct() {
        parent::__construct();
    }
    
    public function indexAction() {
        
    }
    
    public function changeOrderOwnerAction() {
        
    }
    
    public function changeOrderRefuseAction() {
       
    }
    
    /*
     * Obte el temps d'inici i final del script
     */
    public function getTimeScript($start = false){
        if($start){
            $this->time_start = microtime(true);
        }
        else {
            $this->time_end = microtime(true);
        }
    }
    
    
    public function setEvents3DFolders(){
        $this->createModel('photo_Files');
        
        $counter = $this->photo_FilesModel->getPhotoFilesScreensCounter();
        $counter = $counter[0]["counter"];
        
        $photoFiles = $this->photo_FilesModel->getPhotoFilesScreens(500);   
        
        $json = array();
        $i = 0;
        foreach ($photoFiles as $photoFile){
            $json[$i][0] =  $photoFile["id"];
            $json[$i][1] =  $photoFile["path"];
            $i++;
        }
        
//        array_push($json, $counter);
        
        return $json;
    }
    
    public function delEvents3DFolders($arrayFiles){
        $this->createModel('photo_Files');
        
        $response = [1, array()];
        $s = 0;
        
        for ($i = 0; $i < count($arrayFiles); $i++) {
            $id = $arrayFiles[$i][0];
            $path = $arrayFiles[$i][1];
            $oldPath = $arrayFiles[$i][2];

            utils::log("<-->", "logDelEvents3DFolders");
            utils::log("File id = " . $id, "logDelEvents3DFolders");
            utils::log("New Path = " . $path, "logDelEvents3DFolders");
            utils::log("Old Path = " . $oldPath, "logDelEvents3DFolders");
            if(file_exists(G_PATH . $oldPath)){
                if(unlink(G_PATH . $oldPath)){
                    $array = array('path' => $path, 'ServerID' => '2');
                    $upd = $this->photo_FilesModel->updatePhoto_Files($id, $array);

                    if($upd){
                        utils::log("Unlink OK", "logDelEvents3DFolders");
                        utils::log("Update OK", "logDelEvents3DFolders");

                        $s ++;

                        $infoOldPath = pathinfo($oldPath);
                        if($this->checkEmptyDir(G_PATH . $infoOldPath["dirname"])){
                            if(rmdir(G_PATH . $infoOldPath["dirname"])){
                                utils::log("Delete Folder", "logDelEvents3DFolders");
                            }
                            else{
                                utils::log("Fail Delete Folder", "logDelEvents3DFolders");
                                utils::log(G_PATH . $infoOldPath["dirname"], "logDelEvents3DFolders");
                            }
                        }
                    }
                    else {
                        $response[0] = 0;
                        array_push($response[1], "Error01 - Update file $id failed");

                        utils::log("Unlink OK", "logDelEvents3DFolders");
                        utils::log("Fail Update", "logDelEvents3DFolders");
                    }
                }
                else {
                    $response[0] = 0;
                    array_push($response[1], "Error02 - Delete file $id failed");

                    utils::log("Fail Unlink", "logDelEvents3DFolders");
                }
            }
            else{
                utils::log("This $id does not exist deleting from DB", "logDelEvents3DFolders");
                $this->photo_FilesModel->delPhotoFile($id);
            }
        }
        
        if(count($response[1]) >= count($arrayFiles)){
            $response[0] = 0;
            $response[1] = array("Critical Error");
            
            return $response;
        }
        else{
            array_unshift($response[1], "Success - From myphotocode $s SFiles");
            return $response;
        }
    }
    
    public function checkEmptyDir($dir){
        if (!is_readable($dir)) return NULL; 
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            return FALSE;
          }
        }
        return TRUE;
    }
    
    public function check3DFilesExits($arrayFiles){
        $this->createModel('photo_Files');
        
        $j = 0;
        $k = 0;
        for ($i = 0; $i < count($arrayFiles); $i++) {
            $id = $arrayFiles[$i][0];
            $path = $arrayFiles[$i][1];
            
            if(file_exists(G_PATH . $path) === FALSE){
                $this->photo_FilesModel->delPhotoFile($id);
                $j++;
            }
            else{
                $k++;
            }
        }
        
        $response = array();
        $response[0] = 1;
        $response[1] = array("$j Deleted & $k exist in server");
       
        return $response;
        
    }
}
