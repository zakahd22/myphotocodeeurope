<?php

/**
 * Event entity, Object representing an event
 */
class CLD_subDistributors {
    // Table Name
    static public $TableName = "CLD_subDistributors";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "name"
        )
    );
    
    // Fields
    public $id;
    public $name;

    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}