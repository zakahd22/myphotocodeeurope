<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_eventsModel extends trashedModel{
//    public $event;
//    public $events_array;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getEvent($event){
        $this->setFilter('id', '=', $event);
        return $this->select('events');
    }
    
    /*
     * STATISTICS REPORTS FUNCTIONS
     */
    public function stdReport_Years($date1, $date2, $autocreated){
        $this->setFilter('start_date', '>', $date1);
        $this->setFilter('start_date', '<', $date2, "AND");
        $this->setFilter('autocreated', '=', $autocreated, "AND");
        return $this->select('trashed_events', 'count');
    }
    
}