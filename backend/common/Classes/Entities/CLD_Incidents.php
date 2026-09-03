<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_Incidents{
    // Table Name
    static public $TableName = "CLD_Incidents";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "idBooth", "coment", "datetime", "code", "user", "status" 
        ),
        "count" =>array(
            "counter", "id"
        )
    );
    
    // Fields
    public $id;
    public $idBooth;
    public $coment;
    public $datetime;
    public $code;
    public $user;
    public $status;
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