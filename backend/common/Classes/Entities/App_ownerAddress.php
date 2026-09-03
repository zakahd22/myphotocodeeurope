<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_ownerAddress{
    // Table Name
    static public $TableName = "App_ownerAddress";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "idOwner", "preference", "address", "city", "state", "code", "country", "CLD_status", "CLD_type", "CLD_companyName", 
            "CLD_contactName", "CLD_phone"
        )
    );
    
    // Fields
    public $id;
    public $idOwner;
    public $preference;
    public $address;
    public $city;
    public $state;
    public $code;
    public $country;
    public $CLD_status;
    public $CLD_type;
    public $CLD_companyName;
    public $CLD_contactName;
    public $CLD_phone;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}