<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_estadistiques_photos{
    // Table Name
    static public $TableName = "CLD_estadistiques_photos";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "photo", "data", "type_info", "ip", "country", "state", "city"
        ),
        "count" => array(
            "counter", "type_info"
        ),
        "count_" => array(
            "counter", "id"
        ),
        "event_statistics" => array(
            "summation", "counter_photos", "type_info", "photo"
        )
    );
    
    // Fields
    public $id;
    public $photo;
    public $data;
    public $type_info;
    public $ip;
    public $country;
    public $state;
    public $city;
    public $counter;
    public $summation;
    public $counter_photos;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}