<?php

/**
 * Event entity, Object representing an event
 */
class CLD_ownerConnections {
    // Table Name
    static public $TableName = "CLD_ownerConnections";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "user", "type_user", "data", "pais", "state", "ciutat"
        ),
        "StatisticsReport" => array(
            "counter", "id", "pais"
        )
    );
    
    // Fields
    public $id;
    public $user;
    public $type_user;
    public $data;
    public $pais;
    public $state;
    public $ciutat;
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