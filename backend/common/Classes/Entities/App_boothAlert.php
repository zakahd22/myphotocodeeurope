<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_boothAlert{
    // Table Name
    static public $TableName = "App_boothAlert";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "idBooth", "typeAlert", "when", "estat"
        ),
        "count" => array(
            "counter", "id", "idBooth", "typeAlert", "when", "estat"
        ),
    );
    
    // Fields
    public $id;
    public $idBooth;
    public $typeAlert;
    public $when;
    public $estat;
    public $counter;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}