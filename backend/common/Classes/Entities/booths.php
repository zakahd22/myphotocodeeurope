<?php

/**
 * Event entity, Object representing an event
 */
class booths {
    // Table Name
    static public $TableName = "booths";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "dongle", "reference", "rand_string", "rental_id", "seccode", "CLD_Distributor"
        ),
        "distributor" => array (
             "rental_id"
        ),
        "listDongle" => array (
            "id", "dongle", "reference", "rand_string", "seccode", "CLD_Distributor", "rental_id"
        )
    );
    
    // Fields
    public $id;
    public $dongle;
    public $reference;
    public $rand_string;
    public $rental_id;
    public $seccode;
    public $CLD_Distributor;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}