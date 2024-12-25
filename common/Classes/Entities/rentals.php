<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class rentals{
    // Table Name
    static public $TableName = "rentals";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "code", "name", "username", "password", "App_email", "CLD_DistributorId", "token_Sec", 
            "token_SecDate", "ValidatedAlertEmail"
        ),
        "dongles" => array (
            "name"
        ),
        "distributor" => array (
            "id"
        ),
        "count" =>array (
            "counter","id"
        ),
        "listDongle" =>array (
            "name"
        )
        
    );
    
    // Fields
    public $id;
    public $code;
    public $name;
    public $username;
    public $password;
    public $App_email;
    public $CLD_DistributorId;
    public $token_Sec;
    public $token_SecDate;
    public $ValidatedAlertEmail;
    public $counter;

    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}