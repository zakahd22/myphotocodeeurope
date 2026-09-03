<?php

/**
 * list of manuals
 */
class manuals{
    // Table Name
    static public $TableName = "manuals";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "name", "version",
        ),
    );
    
    // Fields
    public $id;
    public $name;
    public $version;

    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}