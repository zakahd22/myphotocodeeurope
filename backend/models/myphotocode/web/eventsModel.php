<?php
require_once G_PATH . "models/baseModel.php";

class eventsModel extends baseModel{
//    public $event;
//    public $events_array;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getEvent($event, $rental_id = FALSE){
        $this->setFilter('id', '=', $event);
        if($rental_id){
            $this->setFilter('rental_id', '=', $rental_id, "AND");
        }
        return $this->select('events');
    }
    
    
    public function getEventCLD_dateLP($dat1, $idEvent, $dat2 = FALSE){
        if($dat2){
            $this->setBetweenFilter('CLD_date_lastPhoto', $dat1, 'AND', $dat2);
            $this->setFilter('id', '=', $idEvent, 'AND');
        }
        else {
            $this->setFilter('id', '=', $idEvent);
            $this->setFilter('CLD_date_lastPhoto', '<', $idEvent, 'AND');
            $this->setFilter('CLD_date_lastPhoto', 'IS', 'NULL', 'OR');
            
        }
        
        return $this->select('events');
    }
    
    
    public function getEventsImport($owner, $event){
        $this->setFilter('rental_id', '=', $owner);
        $this->setFilter('id', '=', $event);
        $this->setGroup('id');
        $this->setOrder('start_date');
        
        return $this->select('events');
    }
    
    /**
     * Get the event from the id
     */
    public function getEventsOwner($owner){
        $this->setFilter('rental_id', '=', $owner);
        return $this->select('events');
    }
    
    public function getEventsOwnerIN($array){
        $this->setInFilter('rental_id', $array);
        return $this->select('events');
    }
    
    public function getEventsIdIN($array){
        $this->setInFilter('id', $array);
        return $this->select('events');
    }
    
    /**
     * Get the event from the ID and Segurity code
     */
    public function getEventsRegister($ID, $code){
        $this->setFilter('id', '=', $ID);
        $this->setFilter('CLD_SecurityCode', '=', $code);
        return $this->select('events');
    }
    
    
     /**
     * Get the events
     */
    public function getEvents($owner = null, $limit = null){
        if($owner != null) $this->setFilter('rental_id', '=', $owner);
        
        $this->setOrder("CLD_date_lastPhoto`, `start_date", "DESC");
        if($limit){ $this->setLimit($limit); }
        utils::log($this->get_sql_string(), logMoment);
        return $this->select('events');
    }
    
    public function getTrashed(){
        $this->setFilter('trashed', 'IS NOT', 'NULL');
        $this->setOrder('id', 'DESC');
        
        return $this->select('events');
    }

    /**
     * Updates the event where event_id = $event
     * 
     * @param String $event The event_id
     * @param Array $updates Containing an array with the strings
     */
    public function updateEvent($event, $updates){
        $this->setFilter('id', '=', $event);
        return $this->update('events', $updates);
    }
    

    /**
     * Deletes the event where event_id = $event
     * 
     * @param String $event The event_id
     */
    public function deleteEvent($event){
        $this->setFilter('id', '=', $event);
        return $this->delete('events');
    }
    
    public function insertEvent(){
        return $this->insert('events');
    }
       
    /**
     * get more than one photo
     * @param type $photoCode all the code or a single part of it 
     * @return Array Containing an array with the entities photos
     */
    public function getAllFromEvents($idDongle ,$initDate, $endDate, $rental_id){
        
        $this->setFilter("photos.booth_id", "=", "$idDongle");
        $this->setBetweenFilter("photos.Appusr_datetime", $initDate,'AND', $endDate);
        if ($rental_id !== false) $this->setFilter("events.rental_id", "=", "$rental_id", "AND");
        $this->setGroup('photos.event_id');
        $this->setOrder('photos`.`event_id', "DESC");

        $fields .= "";
        $this->entity->loadEntity('events');
        $fields .= $this->entity->getEntityFields("count");
        
        $sql = "
            SELECT {$fields}
            FROM events
            LEFT JOIN photos
            ON photos.event_id = events.id  
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'count');
        
        return $result;
    }
    
    public function getAllFromEventsList($owner = false , $limit = false){
        
        if($owner) $this->setFilter('rental_id', '=', $owner);
        if($limit) $this->setLimit($limit);
        $this->setOrder("CLD_date_lastPhoto", "DESC");
        
        return $this->select('events');
    }
    
    
    public function getAllFromEventsListCLD_eventManegerId($ID, $LIMIT = false){
        
        if($ID) $this->setFilter('CLD_eventManegerId', '=', $ID);
        
        $this->setOrder("CLD_date_lastPhoto", "DESC");
        $this->setOrder("start_date", "DESC");
        
        if($LIMIT){
            $this->setLimit($LIMIT);
        }
        
        return $this->select('events');
    }
    
    
    public function getAllFromEventsListIn($in = false , $title = false, $id = false, $owner = false){
        
        if($in){
            $this->setInFilter('rental_id', $in);
        }
        if($title){
            $this->setFilter('title', 'LIKE', '%'.$title.'%');
        }
        if($id){
            $this->setFilter('id', 'LIKE', '%'.$id.'%');
        }
        if($owner){
            $this->setFilter('rental_id', '=', $owner, "AND");
        }
        
        //$this->setFilter("trashed", "IS", "NULL", "AND");
        
        $this->setOrder("CLD_date_lastPhoto", "DESC");
        $this->setOrder("start_date", "DESC");
        
        return $this->select('events');
    }
    
    public function getInfoEventSendPhoto($idEvent){
        $this->setFilter('id', '=', $idEvent);
        return $this->select('events', 'sendPhoto');
    }
    
//    public function getEventFromEventsPhotos($idEvent){
//        $this->setFilter("event_id", "=", $idEvent);
//        $this->setOrder("Appuser_datetime", "DESC");
//        return $this->select('events');
//    }
    
    /*
     * STATISTICS REPORTS FUNCTIONS
     */
    public function stdReport_Years($date1, $date2, $autocreated){
        $this->setFilter('start_date', '>', $date1);
        $this->setFilter('start_date', '<', $date2, "AND");
        $this->setFilter('autocreated', '=', $autocreated, "AND");
        return $this->select('events', 'count');
    }
}