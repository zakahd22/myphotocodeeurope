<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_EventsManegers{
    // Table Name
    static public $TableName = "CLD_EventsManegers";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "name", "surname", "email"
        ),
    );
    
    // Fields
    public $id;
    public $name;
    public $surname;
    public $email;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets["all"] as $entityField){
            $this->{$entityField} = null;
        }
    }
}