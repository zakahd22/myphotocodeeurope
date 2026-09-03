<?php

/**
 * list of relationship between manuals an booths
 */
class manualsBooths{
    // Table Name
    static public $TableName = "manualsBooths";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "booth_id", "manual_id",
        ),
    );
    
    // Fields
    public $id;
    public $booth_id;
    public $manual_id;

    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}