<?php

/**
 * Event entity, Object representing an event
 */
class App_boothBootDC {
    // Table Name
    static public $TableName = "App_boothBootDC";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "idBooth", "idDongle", "UPGRADEid", "idBootDC", "datetimeUpgCheck"
        ),
    );
    
    // Fields
    public $idBooth;
    public $idDongle;
    public $UPGRADEid;
    public $idBootDC;
    public $datetimeUpgCheck;
    
    
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}