<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_Distributors{
    // Table Name
    static public $TableName = "CLD_Distributors";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "code", "Name", "LOCATION", "LATITUDE", "LONGITUDE"
        ),
        "name" => array(
           "Name"
        )
    );
    
    // Fields
    public $id;
    public $code;
    public $Name;
    public $LOCATION;
    public $LATITUDE;
    public $LONGITUDE;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}