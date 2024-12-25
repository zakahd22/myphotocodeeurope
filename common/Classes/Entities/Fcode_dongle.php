<?php

/**
 * Event entity, Object representing an event
 */
class Fcode_dongle {
    // Table Name
    static public $TableName = "Fcode_dongle";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "idDongle", "startDate", "allowTest", "codeAct", "dateAct", "idPB", "codeReset"
        ),
    );
    
    // Fields
    public $idDongle;
    public $startDate;
    public $allowTest;
    public $codeAct;
    public $dateAct;
    public $idPB;
    public $codeReset;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}