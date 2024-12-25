<?php

/**
 * Event entity, Object representing an event
 */
class App_boothConfigDef {
    // Table Name
    static public $TableName = "App_boothConfigDef";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "idBooth", "typeConfig", "value"
        )
    );
    
    // Fields
    public $idBooth;
    public $typeConfig;
    public $value;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}