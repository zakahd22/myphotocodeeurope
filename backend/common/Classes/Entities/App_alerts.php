<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_alerts{
    // Table Name
    static public $TableName = "App_alerts";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "typeAlert", "label", "values", "textAlert"
        ),
    );
    
    // Fields
    public $typeAlert;
    public $label;
    public $values;
    public $textAlert;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}