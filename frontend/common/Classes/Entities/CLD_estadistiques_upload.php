<?php

/**
 * Event entity, Object representing an event
 */
class CLD_estadistiques_upload {
    // Table Name
    static public $TableName = "CLD_estadistiques_upload";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "date", "numUpload"
        ),
        "confirmUpload" => array(
            "numUpload"
        )
    );
    
    // Fields
    public $id;
    public $date;
    public $numUpload;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}