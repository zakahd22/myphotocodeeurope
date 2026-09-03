<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class trashed_CLD_emailsText{
    // Table Name
    static public $TableName = "CLD_emailsText";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "event", "type", "text"
        ),
        "text" => array(
            "text"
        )
    );
    
    // Fields
    public $id;
    public $event;
    public $type;
    public $text;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}