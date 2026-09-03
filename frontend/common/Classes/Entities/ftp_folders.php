<?php

/**
 * photos entity, Object representing a photo
 */
class ftp_folders{
    // Table Name
    static public $TableName = "ftp_folders";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "host", "path", "user", "password"
        ),        
    );
    
    // Fields
    public $id;
    public $host;
    public $path;
    public $user;
    public $password;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}