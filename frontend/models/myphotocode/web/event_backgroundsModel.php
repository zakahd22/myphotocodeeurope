<?php
require_once G_PATH . "models/baseModel.php";

class event_backgroundsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the background from the id
     */
    public function getBackground($background_id){
        $this->setFilter('id', '=', $background_id);
        return $this->select('event_backgrounds');
    }

//    /**
//     * Updates the event where event_id = $event
//     * 
//     * @param String $event The event_id
//     * @param Array $updates Containing an array with the strings
//     */
//    public function updateEvent($event, $updates){
//        $this->setFilter('id', '=', $event);
//        return $this->update('events', $updates);
//    }
//    
//
//    /**
//     * Deletes the event where event_id = $event
//     * 
//     * @param String $event The event_id
//     */
//    public function deleteEvent($event){
//        $this->setFilter('id', '=', $event);
//        return $this->delete('events');
//    }
    
    
}
