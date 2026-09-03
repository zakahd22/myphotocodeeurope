<?php

/**
 * Event entity, Object representing an event
 */
class view_statistic_photos {
    // Table Name
    static public $TableName = "view_statistic_photos";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "code_photo", "type_info", "numShow"
        ),
        "eventInfo" => array (
            "summation", "numShow", "code_photo", "type_info"
        )
    );
    
    // Fields
    public $code_photo;
    public $type_info;
    public $numShow;
    public $summation;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}