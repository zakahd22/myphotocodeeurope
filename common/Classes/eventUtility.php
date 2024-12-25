<?php

class eventUtility {
    public $loghtml = false;
    public $eventFolder = "";
    
    public function __construct() {
    }

    public function setlogHtml($loghtml){
        $this->loghtml = $loghtml;
    }
        
    public function setEventFolder(){
        $this->eventFolder = $eventFolder;        
    }

    /**
     * Check if the $file is a banner
     * 
     * @param String $file
     * @return boolean True if is the banner, False otherwise
     */
    public function notBanner($file){
        return (strpos($file, "banner") == false);
    }
    
    /**
     * Check if the $file is a background
     * 
     * @param String $file
     * @return boolean True if it is not the background, False otherwise
     */
    public function notBackground($file){
        return ($file != "background.jpg");
    }
    
    /**
     * Check if the $file is a hidden file
     * 
     * @param String $file
     * @return boolean True, False otherwise
     */
    public function notHiddenFile($file){
        return ($file != ".") && ($file != "..");
    }
    
    /**
     * Check if the $file is a directory
     * 
     * @param String $file  The file or folder founded inside the dir
     * @param String $dir   The dir without the $file. Example: G_PATH . 'events/201611221181'
     * @return boolean  True if is the banner, False otherwise
     */
    public function notSubDirectory($file, $dir){
        return !is_dir($dir . "/" . $file);
    }
    
    /**
     * Saves the event images to the zip file.
     * Returns true if success, only compress $limit photos each time, 
     * because if more (600) failure, the remaining files would stay
     * at the event folder
     * 
     * @param \ZipArchive $zip
     * @param integer $limit
     * @param string $subdir
     * @return int
     */
    public function saveZipFiles($zip, $limit=400, $subdir=""){
        $compressed_subfolderFiles = 0;
        $count = 0;
        $theorically_added = 0;
        $notempty = 0;
        
        $path = $this->eventFolder . $subdir;
        $event = opendir($path);
        $next_file = readdir($event);

        while($next_file){
            if($this->notHiddenFile($next_file)){
                //counter to know if there are any file or directory in the folder
                $notempty++;
                if($this->notBackground($next_file) && $this->notBanner($next_file)){
                    if($theorically_added < $limit){
//                        utils::echo_( ">>> {$this->eventFolder}{$next_file}", $this->loghtml);
                        if(is_dir($this->eventFolder . $next_file)){
                            $added = $zip->addEmptyDir($next_file);
                            //don't check if exists the subdir in the zip because if is a second execution, it could exist yet 
                            $compressed_subfolderFiles=0;
//                            utils::echo_( "+$next_file", $this->loghtml);
                            $compressed_subfolderFiles = $this->saveZipFiles($zip, ($limit-$theorically_added), "{$subdir}/{$next_file}/");
                            if($compressed_subfolderFiles == -2){
                                return $compressed_subfolderFiles;
                            }
                            else{
//                                utils::echo_( ">>> added ".$compressed_subfolderFiles." files", $this->loghtml);
                                $theorically_added += $compressed_subfolderFiles;
                                $count += $compressed_subfolderFiles;
                            }
                        }
                        else{
                            /*
                            if($subdir!=""){
                                utils::echo_( "\t", $this->loghtml);
                            }
                            utils::echo_( "-$next_file", $this->loghtml);
                            */
                            $count++;
                            $added = $zip->addFile($path . $next_file, "{$subdir}/{$next_file}");
                            if($added){
//                                utils::echo_( ">>> #{$theorically_added} - added", $this->loghtml);
                                $theorically_added++;
                            }
                        }
                    }
                    else {
                        break;
                    }
                }
            }
            $next_file = readdir($event);
        }

        closedir($event);
        if($count == $theorically_added){
            if($notempty==0){
                //empty folder
                return 0;
            }
            //compressed {$theorically_added} files
            return $theorically_added;
        }
        else{
            return -2;
        }
    }
    
}
