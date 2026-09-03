<?php

/**
 * Event entity, Object representing an event
 */
class InstagramSuggestions {
    // Table Name
    static public $TableName = "InstagramSuggestions";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "word", "type", "numCount", "numPrint", "numFollowers", "pais", "isVerified", "fbid"
        ),
    );
    
    // Fields
    public $word;
    public $type;
    public $numCount;
    public $numPrint;
    public $numFollowers;
    public $pais;
    public $isVerified;
    public $fbid;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}