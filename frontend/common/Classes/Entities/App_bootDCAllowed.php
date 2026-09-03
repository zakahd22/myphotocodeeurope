<?php

/**
 * Event entity, Object representing an event
 */
class App_bootDCAllowed {
    // Table Name
    static public $TableName = "App_bootDCAllowed";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "UPGRADEid", "idBootDC", "allowedIds", "response"
        ),
    );
    
    // Fields
    public $UPGRADEid;
    public $idBootDC;
    public $allowedIds;
    public $response;
    
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}