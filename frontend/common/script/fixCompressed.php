<?php
require_once dirname(__FILE__) . '/../global.php';
include G_PATH . 'common/Classes/eventCompressor.php';

class fixCompressed extends eventCompressor{
    
    public function __construct() {
        parent::__construct(false);
    }
    
    public function cronJob(){
        utils::rm_log('logAleix');
        $events = $this->getEventsToCompress();
        foreach($events as $event){
            utils::log($event['id'], 'logAleix');        
            $compression_status = $this->compressEvent($event);
            $this->compressionStatusAction($compression_status, $event['id']);
        }
    }
    
    /**
     * Without limit 100 and partial compression
     * @return array
     */
    public function getEventsToCompress(){
        $event_timeout = date('Y-m-d',strtotime('-1 months', strtotime($this->date)));
        $intEvent_timeout = utils::get_IntDate($event_timeout);
        
        $this->eventsModel->setFilter('CLD_date_lastPhoto', '<', $event_timeout);
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', 'IS', 'NULL', 'AND');
        //Remove, only temporary condition
//        $this->eventsModel->setNotInFilter('id', array(17550, 15736, 17552, 15797, 17406, 17549), 'AND');

        $this->eventsModel->setFilter('CLD_date_lastPhoto', 'IS', 'NULL', 'OR');
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', 'IS', 'NULL', 'AND');
        //Remove, only temporary condition
//        $this->eventsModel->setNotInFilter('id', array(17550, 15736, 17552, 15797, 17406, 17549), 'AND');
        
        $arrayEvents = $this->eventsModel->getEvents();
        return $arrayEvents;
    }
    
    public function compressEvent($event){ 
        $this->compressorUtility->event = $event;
        $this->compressorUtility->eventFolder = $this->compressorUtility->eventsFolder . $this->compressorUtility->event['start_date'] . $this->compressorUtility->event['id'] . "/";
        
        //default do nothing
        $event_compressed = -3;
        
        //compelete event, not compressed yet
        utils::echo_(">>> checking... ___________/events/{$this->compressorUtility->event['start_date']}{$this->compressorUtility->event['id']}...", $this->loghtml);
        if($this->compressorUtility->existZipFile()){
            utils::echo_( ">>> possibly compressed at ___________/events/compressed_events/{$this->compressorUtility->event['id']}_compressed.zip", $this->loghtml);
            if(is_dir($this->compressorUtility->eventFolder)){
                if($this->isEmptyEventDir()){
                    //event compressed
                    $event_compressed = 0;
                }
                //event partially compressed
                $this->setEventAsPartialCompressed($this->compressorUtility->event['id']);
            }
            else{
                //Event dir does not exist
                $event_compressed = -1;                
            }
        } else {
            utils::echo_(">>> No Zip File!...", $this->loghtml);
            if(is_dir($this->compressorUtility->eventFolder)){
                if($this->isEmptyEventDir()){
                    //event compressed only if empty event_dir
                    $event_compressed = 0;
                 }
            }
            else{
                //Event dir does not exist
                $event_compressed = -1;                
            }
        }
        
        return $event_compressed;
    }
    
    private function isEmptyEventDir($subdir=""){
        $result = true;
        $path = $this->compressorUtility->eventFolder . $subdir;
        $event = opendir($path);
        $next_file = readdir($event);

        while($next_file){
            if($this->compressorUtility->notHiddenFile($next_file)){
                //counter to know if there are any file or directory in the folder
                $notempty++;
                if($this->compressorUtility->notBackground($next_file) && $this->compressorUtility->notBanner($next_file)){
                    $result = false;
                }
            }
            $next_file = readdir($event);
        }

        closedir($event);
       
        return $result;
    }
}

utils::echo_(">>> Starting DB Compressor fix", false);
new fixCompressed();