<?php

/**
 * Event entity, Object representing an event
 */
class statistics_photo_files {
    // Table Name
    static public $TableName = "statistics_photo_files";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "owner", "date", "nFiles"
        ),
        "count" => array (
            "counter","id"
        ),
        "sum" => array (
            "summation_", "nFiles"  
        ),
    );
    
    // Fields
    public $id;
    public $owner;
    public $date;
    public $nFiles;
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