<?php

/**
 * Event entity, Object representing an event
 */
class statistics_photos {
    // Table Name
    static public $TableName = "statistics_photos";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "code_photo", "event", "date", "type_info", "state", "country", "num_show"
        ),
        "count" => array (
            "counter", "id", "num_show"
        ),
        "sum" => array (
            "summation_", "num_show"  
        ),
    );
    
    // Fields
    public $id;
    public $code_photo;
    public $event;
    public $date;
    public $type_info;
    public $state;
    public $country;
    public $num_show;
    public $counter;
    public $summation_;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}