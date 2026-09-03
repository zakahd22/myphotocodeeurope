<?php

/**
 * photos entity, Object representing a photo
 */
class registre_emails{
    // Table Name
    static public $TableName = "registre_emails";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "email", "event_id", "fecha"
        )
    );
    
    // Fields
    public $id;
    public $email;
    public $event_id;
    public $fecha;

    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}