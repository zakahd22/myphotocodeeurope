<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_questions_emails{
    // Table Name
    static public $TableName = "CLD_questions_emails";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "event", "email"
        ),
    );
    
    // Fields
    public $id;
    public $event;
    public $email;
    public $count;


    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}