<?php
require_once dirname(__FILE__) . '/../global.php';
require_once G_PATH . 'common/Classes/baseController.php';
require_once G_PATH . 'common/Classes/compressorUtility.php';

class cleanFTPFolders extends baseController{
    public $ftpFolders = "uploads/";
    public $deletedFiles = 0;
    
    public function __construct($html = false) {
        parent::__construct();
        $this->date = date("Ymd");
        $this->createModels();
        
        $this->cronJob();
    }
    
    public function createModels(){
        $this->createModel('ftp_folders');
    }
    
    public function cronJob(){
        $ftp_folders = $this->ftp_foldersModel->getAllftp_folders();
        foreach($ftp_folders as $ftp_folder){
            $uploadFolder = G_PATH . $this->ftpFolders . $ftp_folder['path'];
//            $uploadFolder = G_PATH . "uploads/test/";
            if(is_dir($uploadFolder)){
                $dir = new DirectoryIterator($uploadFolder);
//                utils::log($uploadFolder, "logFTP");
//                utils::log("====================", "logFTP");
                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot()) {
//                        utils::log($fileinfo->getFilename(), "logFTP");
                        $this->deleteFile($uploadFolder . "/" . $fileinfo->getFilename());
                    }
                }
            }
        }
    }
    
    public function deleteFile($file_to_delete){
        $date = date_create();
        date_modify($date, "-1 day");
        $yesterday =  date_format($date, 'Y/m/d');
        $file_modified_date = date ("Y/m/d", filemtime($file_to_delete));

//        utils::log("{$file_modified_date} < {$yesterday}", "logFTP");
        if($file_modified_date < $yesterday){
//            utils::log("toDelete", "logFTP");
            if(unlink($file_to_delete)) $this->deletedFiles++;
        }
    }
}