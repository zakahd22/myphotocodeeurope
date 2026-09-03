<?php

/**
 * list of items in each manual
 */
class manualsItems{
    // Table Name
    static public $TableName = "manualsItems";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "manual_id", "type", "data", "desc"
        ),
    );
    
    // Fields
    public $id;
    public $manual_id;
    public $type;
    public $data;
    public $desc;

    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}