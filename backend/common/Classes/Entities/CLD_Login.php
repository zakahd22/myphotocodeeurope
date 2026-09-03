<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_Login{
    // Table Name
    static public $TableName = "CLD_Login";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "username", "password", "id_user", "userType", "banned",
        ),
        "users" => array(
            "username", "id_user",
        ),
    );
    
    // Fields
    public $username;
    public $password;
    public $id_user;
    public $userType;
    public $banned;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}