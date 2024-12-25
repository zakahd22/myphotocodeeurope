<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_boothDongle{
    // Table Name
    static public $TableName = "App_boothDongle";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "idBooth", "idDongle", "datetimeS", "datetimeF"
        ),
        "idBooth" => array(
             "idBooth"
        )
    );
    
    // Fields
    public $idBooth;
    public $idDongle;
    public $datetimeS;
    public $datetimeF;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}