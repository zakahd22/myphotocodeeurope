<?php
require_once G_PATH . 'common/Classes/eventUtility.php';

class compressorUtility extends eventUtility{  
    public $compressedStatus = 0;
    public $filelimit_compression = 400;
    
    public $eventsFolder = "";
    public $compressedZip = "";
    
    public $event = array();
    
    public function __construct($html = false) {
        parent::__construct();
        $this->loghtml = $html;

        $this->initVars();
    }
    
    public function initVars(){
        $this->eventsFolder = G_PATH . "events/";
        $this->compressedStatus = 0;
    }
        
    public function setFileLimitCompression($limit){
        $this->filelimit_compression = $limit;
    }
        
    public function getFileLimitCompression(){
        return $this->filelimit_compression;
    }

    /**
     * public method to compress an event
     * 
     * @param Integer $event_id The event entity array to compress
     */
    public function compressEvent($event){       
        $this->event = $event;
        $this->eventFolder = $this->eventsFolder . $this->event['start_date'] . $this->event['id'] . "/";
        
        //compelete event, not compressed yet
        utils::echo_(">>> compressing... ___________/events/{$this->event['start_date']}{$this->event['id']}...", $this->loghtml);
        if($this->existZipFile()){
            utils::echo_( ">>> partially compressed at--- ___________/events/compressed_events/{$this->event['id']}_compressed.zip", $this->loghtml);
            $this->compressedStatus = 1;
        }
        
        $event_compressed = $this->compress();
        return $event_compressed;
    }
    
    private function compress(){
        $compressed = -3;
        if(is_dir($this->eventFolder)){
            utils::echo_("compressing at {$this->compressedZip}", $this->loghtml);
            $zip = new ZipArchive();
            $zip_open = $zip->open($this->compressedZip, ZIPARCHIVE::CREATE);
            if($zip_open){
                $compressed = $this->saveZipFiles($zip, $this->filelimit_compression);
                $zip->close();
            }
        }
        else{
            $compressed = -1;
        }
        return $compressed;
    }

    public function existZipFile(){
        $this->compressedZip = "{$this->eventsFolder}compressed_events/{$this->event['id']}_compressed.zip";
//        utils::echo_( "consulting if {$zipname}", $this->loghtml);
        return file_exists($this->compressedZip);
    }

    /**
     * Deletes the zip file. Returns true if success
     * 
     * @param type $event_id
     * @return type
     */
    public function deleteZip($event_id){
        $deleted = false;
        utils::echo_("deleting... {$this->compressedZip}", $this->loghtml);
        if(file_exists($this->compressedZip)){
            $deleted = unlink($this->compressedZip);
        }
        return $deleted;
    }
    
    /* Delete images */
    public function deleteIMG($limit, $subdir=""){
        $path = $this->eventFolder . $subdir;
        if(is_dir($path)){
            $image = opendir($path);
            $count = 0;
            $notempty=0;
            $theorically_deleted = 0;

            $next_file = readdir($image);

            while($next_file){
                if($this->notHiddenFile($next_file)){
                    //counter to know if there are any file or directory in the folder
                    $notempty++;
                    if($this->notBackground($next_file) && $this->notBanner($next_file)){
                        if($theorically_deleted < $limit){
                            if (is_dir($path . $next_file)){
//                                utils::echo_("+$next_file", $this->loghtml);
                                $deleted_subfolderFiles = 0;
                                $deleted_subfolderFiles = $this->deleteIMG(($limit-$theorically_deleted), "{$subdir}/{$next_file}/");
                                $theorically_deleted += $deleted_subfolderFiles;
//                                utils::echo_(">>> deleted ".$deleted_subfolderFiles." files", $this->loghtml);
                                if($theorically_deleted < $limit){
                                    $deleted=rmdir($path . $next_file);
                                    if($deleted){
//                                        utils::echo_("folder ".$dir."/".$next_file." deleted", $this->loghtml);
                                    }
                                }
                            }
                            else{
                                $deleted = unlink($path . $next_file);
                                $count++;
                                if($deleted){
                                    /*
                                    if($subdir == 1){
                                        echo "\t";
                                    }
                                    */
//                                    utils::echo_("-$next_file", $this->loghtml);
                                    $theorically_deleted++;
                                }
                                else{
                                    break;
                                }
                            }
                        }
                        else{
                            break;
                        }
                    }
                }
                $next_file = readdir($image);
            }
            closedir($image);
            return $theorically_deleted;
        }
        return -1;
    }
    
    /**
     * public method to uncompress an event
     * 
     * @param Integer $event_id The id of the event to compress
     */
    public function uncompressEvent($event_id){
        
    }
    
    /**
     * Public method
     * Return content files zip in array.
     * 
     * @param type $path
     */
    public function iterateCompressedEventZip($path){
        $_zip = new ZipArchive();
        $contentZip = array();
        if ($_zip->open($path) === TRUE) {            
            $array_compressed = array();
            for( $i = 0; $i < $_zip->numFiles; $i++ ){ 
                array_push($contentZip, $_zip->statIndex( $i ));
            }
        }
        else{
            utils::log("Could not open this zip -> {$path}", 'logCompressorUtility', 'iterateCompressedEventZip');
            $contentZip = false;
        }
        return $contentZip;
    }
}
