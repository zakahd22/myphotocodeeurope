<?php

/**
 * photos entity, Object representing a photo
 */
class gestor{
    // Table Name
    static public $TableName = "gestor";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "code", "method", "contact", "timestamp", "state", "last", "error", "versioPB", "idb", "vist", "token"
        )
    );
    
    // Fields
    public $id;
    public $code;
    public $method;
    public $contact;
    public $timestamp;
    public $state;
    public $last;
    public $error;
    public $versioPB;
    public $idb;
    public $vist;
    public $token;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}