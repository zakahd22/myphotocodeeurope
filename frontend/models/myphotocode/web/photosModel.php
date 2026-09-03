<?php
require_once G_PATH . "models/baseModel.php";

class photosModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * LookPhoto
     * get a single photo
     * 
     * @param type $code
     * @return Array Containing an array with the entity photos
     */
    public function getPhoto($code=false){
        if($code){
            $this->setFilter('code', 'LIKE', $code);
        }
        return $this->select('photos');
    }
    
    /**
     * Events
     * 
     * get more than one photo
     * @param type $photoCode all the code or a single part of it 
     * @return Array Containing an array with the entities photos
     */
    public function getPhotos($event_id){
        $this->setOrder("Appusr_datetime", "DESC");
//        if ($photoCode !== false) $this->setFilter("code", "LIKE", "%{$photoCode}%");
        $this->setFilter("event_id", "=", "{$event_id}");
        return $this->select('photos');
    }
    
    
    public function getPhotosDates($event_id, $date1, $date2){
        $this->setBetweenFilter("Appusr_datetime", $dat1, "AND", $date2);
        $this->setFilter("event_id", "=", "{$event_id}", "AND");
        return $this->select('photos');
    }
    
    public function getPhotosByEvent($event_id, $flag){
        $this->setFilter("event_id", "=", "{$event_id}");
        $this->setFilter("flag", "=", "0", "AND");
        return $this->select('photos');
    }
    
    public function getPhotosCountBooth($event_id){
        $this->setFilter("event_id", "=", $event_id);
        $this->setGroup('booth_id');
        return $this->select('photos', 'countBooth_id');
    }
    
    /**
     * get more than one photo
     * @param type $photoCode all the code or a single part of it 
     * @return Array Containing an array with the entities photos
     */
    public function getAllFromPhotos($photoCode = false){
        $fields .= "";
        $this->setOrder("photos`.`Appusr_datetime", "DESC");
        if ($photoCode !== false) $this->setFilter("photos.code", "LIKE", "%{$photoCode}%");
        $this->entity->loadEntity('photos');
        $fields .= $this->entity->getEntityFields();  
        $fields .= ', ';  
        $this->entity->loadEntity('events');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields} 
            FROM photos
            LEFT JOIN events
            ON events.id = photos.event_id
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all', array('photos','events'));
        
        return $result;
    }
    
    
    
    public function updatePhoto($code, $updates){
        $this->setFilter('code', '=', $code);
        return $this->update('photos', $updates);
    }
    
    public function updatePhotoArrayIn($array, $updates){
        $this->setInFilter('code', $array);
        return $this->update('photos', $updates);
    }
    
    public function updatePhotoByEvent($event_id, $updates, $code = false){
        $this->setFilter('event_id', '=', $event_id);
        if($code) $this->setFilter('code', '=', $code, "AND");
        return $this->update('photos', $updates);
    }
    
    /**
     * Updates the event where event_id = $event
     * 
     * @param String $event The event_id
     * @param Array $updates Containing an array with the strings
     */
    
    public function updatePhotoAsId($photo_id, $updates){
        $this->setFilter('id', '=', $photo_id);
        $this->setFilter("pbs_id", "=", 0, "AND");
        return $this->update('photos', $updates);
    }
    

    
    public function insertPhoto(){
        return $this->insert('photos');
    }
    
    public function countPhotosInEvent($event_id){
        $this->setFilter('event_id', '=', $event_id);
        return $this->select('photos', 'count');
    }

    
    public function getAllPhotosFromPbs($idDongle, $datetimeS, $datetimeF, $user_id = false, $order = false){
        $this->setTable("photos");
        $fields .= "";
        $this->entity->loadEntity('photos');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields} 
            FROM photos
            LEFT JOIN events
            ON events.id = photos.event_id
        ";
        
        $this->setFilter("photos.booth_id", "=", "{$idDongle}");
        $this->setBetweenFilter("Appusr_datetime", $datetimeS, "AND", $datetimeF, "AND");
        if ($user_id !== false){
            $this->setFilter("events.rental_id", "=", "$user_id", "AND");
        }
        if($order !== false){
            $this->setGroup("event_id");
            $this->setOrder("event_id", "DESC");
        }
        
        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all');
        
        return $result;
    }
    
    
    public function getCodePhotosOwner($owner){
        $fields .= "";
        $this->setFilter("events.rental_id", "=", "{$owner}");
        $this->entity->loadEntity('photos');
        $fields .= $this->entity->getEntityFields();  
      
        $sql = "
            SELECT {$fields} 
            FROM photos
            LEFT JOIN events
            ON events.id = photos.event_id
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all');
        
        return $result;
    }
    
    public function getCodePhotosOwnerIn($owner){
        $fields .= "";
        $this->entity->loadEntity('photos');
        $fields .= $this->entity->getEntityFields();  
      
        $sql = "
            SELECT {$fields} 
            FROM photos
            LEFT JOIN events
            ON events.id = photos.event_id
        ";
        
        $this->setInFilter("events.rental_id", $owner);

        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all');
        return $result;
    }    
    
    public function getPhotosByCLD_pDelete(){
        $this->setFilter('CLD_pDelete', '=', 1);
        $this->setLimit(100);
        return $this->select('photos');
    }
    
    /*
     * Used in list events in photobooths list (no owner user)
     */
    public function getPhotosScript($dongle_id, $datetimeS, $datetimeF, $order = false){
        $this->setTable("photos");
        $this->setFilter("booth_id", "=", $dongle_id);
        $this->setBetweenFilter("Appusr_datetime", $datetimeS, "AND", $datetimeF, "AND");
        if($order !== false){
            $this->setGroup("event_id");
            $this->setOrder("event_id", "DESC");
        }
        return $this->select('photos');
    }
    
    public function getEventPhoto($code=false){
        $return = false;
        if($code){
            $this->setFilter("code", "LIKE", $code);
            $return = $this->select('photos');
        }
        return $return;
    }

    public function getCLD_pDeleteEvents($ID_EVENT){
        if($ID_EVENT){
            $this->setTable("photos");
            $this->setFilter("event_id", "=", $ID_EVENT);
            $this->setFilter("CLD_pDelete", "=", '1', 'AND');
            return $this->select('photos', 'count');
        }
        else {
            return false;
        }
    }
    
    public function getCLD_pDeleteEvents_comprimides($ID_EVENT){
        if($ID_EVENT){
            $this->setTable("photos");
            $this->setFilter("event_id", "=", $ID_EVENT);
            $this->setFilter("CLD_pDelete", "=", '0', 'AND');
            return $this->select('photos', 'count');
        }
        else {
            return false;
        }
    }

    public function getPhotoPBIdByCode($code){
        if(is_string($code) && !empty($code)){
            $this->setTable("photos");
            $this->setFilter("code", "=", $code);
            return $this->select('photos', 'pbs_id');
        } else {
            return false;
        }
    }
    
    
    /*
     * STATISTICS FUNCTIONS
     */
    public function getStatisticReportInfo($date1, $date2){
        $this->setBetweenFilter('Appusr_datetime', $date1, 'AND', $date2);
        return $this->select('photos', 'count');
    }
    
}