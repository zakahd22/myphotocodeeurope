<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_questions{
    // Table Name
    static public $TableName = "CLD_questions";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "question_number", "question", "reply1", "reply2", "r1", "r2", "event"
        )
    );
    
    // Fields
    public $id;
    public $question_number;
    public $question;
    public $reply1;
    public $reply2;
    public $r1;
    public $r2;
    public $event;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}