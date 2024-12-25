<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_info{
    // Table Name
    static public $TableName = "App_info";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            'idInfo', 'when', 'idBooth', 'idDongle', 'typeInfo', 'money', 'money2', 'currency', 'stock', 'i1', 'i2', 'i3', 'i4', 'i5', 'str1', 'str2', 'PBnew', 'in1', 'in2', 'in3', 'in4', 'in5', 'in6', 'in7', 'in8', 'pbs_time', 'db_time'
        ),
    );
    
    // Fields
    public $idInfo;
    public $when; 
    public $idBooth; 
    public $idDongle; 
    public $typeInfo; 
    public $money; 
    public $money2; 
    public $currency; 
    public $stock; 
    public $i1; 
    public $i2; 
    public $i3; 
    public $i4; 
    public $i5; 
    public $str1; 
    public $str2; 
    public $PBnew; 
    public $in1; 
    public $in2; 
    public $in3; 
    public $in4; 
    public $in5; 
    public $in6; 
    public $in7; 
    public $in8; 
    public $pbs_time; 
    public $db_time;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}