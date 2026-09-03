<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_Inc_coments{
    // Table Name
    static public $TableName = "CLD_Inc_coments";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "coment", "incident", "datetime", "user"
        ),
    );
    
    // Fields
    public $id;
    public $coment;
    public $incident;
    public $datetime;
    public $user;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}