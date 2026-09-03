<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_boothTypes{
    // Table Name
    static public $TableName = "CLD_boothTypes";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "char", "name", "CLD_modelSN", "CLD_lastSN"
        ),
        "name" => array(
            "id","name"
        )
    );
    
    // Fields
    public $id;
    public $char;
    public $name;
    public $CLD_modelSN;
    public $CLD_lastSN;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}