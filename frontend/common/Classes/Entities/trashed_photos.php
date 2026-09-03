<?php

/**
 * photos entity, Object representing a photo
 */
class trashed_photos{
    // Table Name
    static public $TableName = "photos";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "code", "event_id", "booth_id", "flag", "Appusr_datetime", "CLD_pDelete", "pbs_id"
        ),
        "lookphotos" => array (
            "id", "event_id", "flag", "Appusr_datetime"
        ),
        "count" => array (
            "counter","id"
        ),
        "countBooth_id" => array (
            "counter", "id" , "pbs_id", "booth_id"
        ),
        "confirmupload" => array (
            "code", "event_id", "booth_id", "Appusr_datetime"
        )
        
        
    );
    
    // Fields
    public $id;
    public $code;
    public $event_id;
    public $booth_id;
    public $flag;
    public $Appusr_datetime;
    public $CLD_pDelete;
    public $counter;
    public $pbs_id;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}