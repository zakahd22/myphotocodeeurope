<?php

/**
 * Event entity, Object representing an event
 */
class Fcode_reg {
    // Table Name
    static public $TableName = "Fcode_reg";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id","idDongle", "dateEnd", "gracePlays", "code", "puk", "codeSent", "pukSent", "disabled"
        ),
    );
    
    // Fields
    public $id;
    public $idDongle;
    public $dateEnd;
    public $gracePlays;
    public $code;
    public $puk;
    public $codeSent;
    public $pukSent;
    public $disabled;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}