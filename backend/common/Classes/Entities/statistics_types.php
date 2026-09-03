<?php

/**
 * Event entity, Object representing an event
 */
class statistics_types {
    // Table Name
    static public $TableName = "statistics_types";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "name", "statistics_table_name", "description"
        ),
        "table" => array (
            "statistics_table_name"
        )
    );
    
    // Fields
    public $id;
    public $name;
    public $statistics_table_name;
    public $description;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}