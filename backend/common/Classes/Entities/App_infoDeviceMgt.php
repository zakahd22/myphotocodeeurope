<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_infoDeviceMgt{
    // Table Name
    static public $TableName = "App_infoDeviceMgt";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "idDM", "when", "idBooth", "idDongle", "device", "model", "error", "attempts" , "resp", "action", "ok", "sent", "pbs_time", "db_time"
        ),
        "count" =>array(
            "counter", "idDM"
        )
    );
    
    // Fields
    public $idDM;
    public $when;
    public $idBooth;
    public $idDongle;
    public $device;
    public $model;
    public $error;
    public $attempts;
    public $resp; 
    public $action;    
    public $ok;    
    public $sent; 
    public $pbs_time;    
    public $db_time;   
    
    
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}