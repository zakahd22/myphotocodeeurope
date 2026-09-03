<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_boothAlertDef{
    // Table Name
    static public $TableName = "App_boothAlertDef";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "idBooth", "typeAlert", "value" 
        )
    );
    
    // Fields
    public $idBooth;
    public $typeAlert;
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