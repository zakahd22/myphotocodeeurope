<?php

class App_sessions{
    // Table Name
    static public $TableName = "App_sessions";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "idBooth", "idDongle", "start", "last"
        ),
    );
    
    // Fields
    public $id;
    public $idBooth;
    public $idDongle;
    public $start;
    public $last;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}