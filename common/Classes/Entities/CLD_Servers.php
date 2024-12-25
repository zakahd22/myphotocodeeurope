<?php

/**
 * Event entity, Object representing an event
 */
class CLD_Servers {
    // Table Name
    static public $TableName = "CLD_Servers";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "name", "absolutePath", "domain", "ip"
        )
    );
    
    // Fields
    public $id;
    public $name;
    public $absolutePath;
    public $domain;
    public $ip;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}