<?php

//require_once dirname(__FILE__) . '/../global.php';
require_once G_PATH . 'common/Classes/baseController.php';
require_once G_PATH . 'common/Classes/compressorUtility.php';

class eventCompressor extends baseController{
    
//    $compression_date not working...
//    public static $compression_date = '-1 months';
    public $compressorUtility;
    
    public $loghtml = false;    
    public $filelimit_compression = 400;    
    public $date = null;
    /**
     *  ID events that can not be compressed
     * @var array
     */
    public $DCevents =  array(17550, 15736, 17552, 15797, 17406, 17549);
    
    public function __construct($html = false) {
        parent::__construct();
        $this->date = date("Ymd");
        $this->createModels();
        
        $this->compressorUtility = new compressorUtility();
        $this->compressorUtility->setlogHtml($html);
        $this->compressorUtility->setFileLimitCompression($this->filelimit_compression);
        
        $this->cronJob();
    }
    
    public function createModels(){
        $this->createModel('events');
        $this->createModel('photos');
    }
    
    public function cronJob(){
        utils::rm_log('logAleix');
        $events = $this->getEventsToCompress();
        foreach($events as $event){
            utils::log($event['id'], 'logAleix');        
            $compression_status = $this->compressorUtility->compressEvent($event);
            $this->compressionStatusAction($compression_status, $event['id']);
        }
    }
    
    public function getEventsToCompress(){
        $event_timeout = date('Y-m-d',strtotime('-1 months', strtotime($this->date)));
        $intEvent_timeout = utils::get_IntDate($event_timeout);
        
        $this->eventsModel->setFilter('CLD_date_lastPhoto', '<', $event_timeout);
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', 'IS', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', 'IS NOT', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', '<', $intEvent_timeout, 'AND');
        //Remove, only temporary condition
        $this->eventsModel->setNotInFilter('id', $this->DCevents, 'AND');
        
        $this->eventsModel->setFilter('CLD_date_lastPhoto', '<', $event_timeout, 'OR');
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', '<', '0', 'AND');
        $this->eventsModel->setFilter('lastCompressed', 'IS NOT', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', '<', $intEvent_timeout, 'AND');
        //Remove, only temporary condition
        $this->eventsModel->setNotInFilter('id', $this->DCevents, 'AND');
        
        $this->eventsModel->setFilter('CLD_date_lastPhoto', 'IS', 'NULL', 'OR');
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', 'IS', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', 'IS NOT', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', '<', $intEvent_timeout, 'AND');
        //Remove, only temporary condition
        $this->eventsModel->setNotInFilter('id', $this->DCevents, 'AND');
        
        $this->eventsModel->setFilter('CLD_date_lastPhoto', 'IS', 'NULL', 'OR');
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', '<', '0', 'AND');
        $this->eventsModel->setFilter('lastCompressed', 'IS NOT', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', '<', $intEvent_timeout, 'AND');
        //Remove, only temporary condition
        $this->eventsModel->setNotInFilter('id', $this->DCevents, 'AND');
        
        $this->eventsModel->setFilter('CLD_date_lastPhoto', '<', $event_timeout, 'OR');
        $this->eventsModel->setFilter('start_date', '<', $intEvent_timeout, 'AND');
        $this->eventsModel->setFilter('compressed', 'IS NOT', 'NULL', 'AND');
        $this->eventsModel->concatWhere('UNIX_TIMESTAMP(`compressed`) < UNIX_TIMESTAMP(`CLD_date_lastPhoto`)', 'AND');
        $this->eventsModel->setFilter('lastCompressed', 'IS NOT', 'NULL', 'AND');
        $this->eventsModel->setFilter('lastCompressed', '<', $intEvent_timeout, 'AND');
        //Remove, only temporary condition
        $this->eventsModel->setNotInFilter('id', $this->DCevents, 'AND');
        
        $this->eventsModel->setLimit('100');
        utils::log("Query getEventsToCompress {$this->eventsModel->sql_string}", "logCronCleanEvents", "getEventsToCompress");    
        $arrayEvents = $this->eventsModel->getEvents();
        
        return $arrayEvents;
    }
    
    public function compressionStatusAction($status, $event_id){
        utils::log("Status = {$status} --- LIMIT = {$this->filelimit_compression}", "logAleix", "compressionStatusAction");
        switch($status){
            case 0:
                $this->emptyEventDir($status, $event_id);
                break;
            
            case -1:
                $this->notEventDir($event_id);
                break;

            case -2:
                $this->failedToCompress($status, $event_id);
                break;

            case -3:
                $this->notCompressible($event_id);
                break;

            case $status == $this->filelimit_compression:
                $this->partialCompression($event_id);
                break;
            
            default:
                $this->successCompressed($event_id);
                break;            
        }
    }

    public function emptyEventDir($state, $event_id){
        if(!$state){
            //First Execution, event directory empty, not used in 6 months, we could delete it from the db?
            utils::echo_( ">>> Event directory empty ", $this->loghtml);
            utils::echo_( ">>> SUCCESS 03", $this->loghtml);
        }
        else{
            //Second Execution, totally compressed, set as compressed
            utils::echo_(">>> Event directory totally compressed", $this->loghtml);
            utils::echo_( ">>> SUCCESS 04 ", $this->loghtml);
        }
        
        $this->setEventAsCompressed($event_id);
        utils::echo_(">>> Event Set as compressed", $this->loghtml);
    }
    
    /**
     * event directory does not exist, remove event from the db? 
     * It would be so much destructive, it wouldn't let any signal of the event anywhere 
     * and if we want provide to somebody the event backup, 
     * it would be impossible to find because we would need the id instanced in the db...
     * 
     * Instead of that, it's necessary to avoid iterate always the same events, we have to filter them, 
     * if not so unused events will finish compressed and it could NOT be. Events that had been compressed 
     * are safe because they have a compressed field not null.
     */
    public function notEventDir($event_id){
        utils::echo_(">>> Event directory does not exist ", $this->loghtml);
        
        $this->setEventAsCompressed($event_id);
        
        utils::echo_(">>> Event Set as compressed", $this->loghtml);
        utils::echo_(">>> SUCCESS 02", $this->loghtml);
    }
    
    /**
     * First execution!, if failed to compress we can delete the zip and 
     * we wouldn't loose any file.
     * 
     * @param type $event_id
     */
    public function failedToCompress($state, $event_id){
        if(!$state){
            utils::echo_( ">>> FAILED to compress! ______{$this->compressorUtility->eventFolder}", $this->loghtml);
            utils::echo_( ">>> deleting zip file... _____{$this->compressorUtility->compressedZip}", $this->loghtml);
            $zipdeleted = $this->compressorUtility->deleteZip();
            if($zipdeleted){
                utils::echo_( ">>> zip deleted ______________{$this->compressorUtility->compressedZip}", $this->loghtml);
                utils::echo_( ">>> ERROR 01 ", $this->loghtml);
            }
            else{
                utils::echo_( ">>> zip NOT deleted __________{$this->compressorUtility->compressedZip}", $this->loghtml);
                utils::echo_( ">>> ERROR 02 ", $this->loghtml);
            }
        }
        else{
            //We will stay the db field marked as partially compressed, next execution will compress it.
            utils::echo_( ">>> FAILED to compress! ______{$this->compressorUtility->eventFolder}", $this->loghtml);
            utils::echo_( ">>> ERROR 03", $this->loghtml);
        }
    }
    
    public function notCompressible($event_id){
        //an event with files inside but anything are photos or videos.
        utils::echo_(">>> Event directory could not be compressed, not valid files inside", $this->loghtml);
//        utils::echo_(">>> Set as compressed anyway, no zip generated", $this->loghtml);
//        $this->setEventAsCompressed($event_id);
    
        utils::echo_(">>> ERROR 00", $this->loghtml);
    }
    
    /**
     * event directory contain more than 400 files, we have to remove only 400 photos
     * 
     * @param type $event_id
     */
    public function partialCompression($event_id){
        utils::echo_(">>> Event directory contain more photos", $this->loghtml);
        utils::echo_(">>> compressed _______________{$this->compressorUtility->eventFolder} >> {$this->compressorUtility->compressedZip}", $this->loghtml);
        $this->setEventAsPartialCompressed($event_id);
        utils::echo_(">>> deleting {$this->filelimit_compression} files... _______{$this->compressorUtility->eventFolder}...", $this->loghtml);
        $deletedEvent = $this->compressorUtility->deleteIMG($this->filelimit_compression);

        utils::echo_(">>> SUCCESS 01", $this->loghtml);
    }
    
    
    public function successCompressed ($event_id){
        utils::echo_(">>> compressed _______________{$this->compressorUtility->eventFolder} >> {$this->compressorUtility->compressedZip}", $this->loghtml);
        $this->setEventAsCompressed($event_id);
        utils::echo_(">>> deleting... ______________{$this->compressorUtility->eventFolder}...", $this->loghtml);
        $deletedEvent =  $this->compressorUtility->deleteIMG($this->filelimit_compression);
        if($deletedEvent != -1){
            utils::echo_(">>> deleted {$deletedEvent} files_________{$this->compressorUtility->eventFolder}", $this->loghtml);
            utils::echo_(">>> SUCCESS 05", $this->loghtml);
        }
        else{
            //probably some subFolder of *-3D type, for the moment don't do anything
            utils::echo_(">>> Failed to delete, maybe some subFolder (*-3D) inside? Don't working well!!!!", $this->loghtml);
            utils::echo_(">>> ERROR 06", $this->loghtml);
        }
    }
    
    public function setEventAsCompressed($event_id){
        $updates = array("compressed"=>$this->date);
        return $this->eventsModel->updateEvent($event_id, $updates);
    }
    
    public function setEventAsPartialCompressed($event_id){
        $updates = array("compressed"=>-$this->date);
        return $this->eventsModel->updateEvent($event_id, $updates);
    }
}