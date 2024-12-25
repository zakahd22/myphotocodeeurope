<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_historyBooth{
    // Table Name
    static public $TableName = "CLD_historyBooth";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "comment", "data", "idBooth", "sn"
        ),
    );
    
    // Fields
    public $id;
    public $comment;
    public $data;
    public $idBooth;
    public $sn;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}