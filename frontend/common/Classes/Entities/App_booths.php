<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class App_booths{
    // Table Name
    static public $TableName = "App_booths";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "idBooth", "estat", "type", "owner", "name", "obs", "serialnumber", "location", "latitude", 
            "longitude", "lastConnLocal", "lastConn", "timeZone", "lastConnZone", "alertOffline", "hS", "mS", "hE", "mE",
            "hmS", "hmE", "midnight", "report", "cardReaderSN", "payPalVendor", "version", "CLD_Status",
            "CLD_date_start_production", "CLD_date_production", "CLD_date_sold", "CLD_date_tOwner",
            "CLD_ControlQuality", "CLD_Distributor", "CLD_idType", "CLD_subDistributor", "CLD_production",
            "PBtwid"
        ),  
        "distributor" => array(
             "owner"
        ),
        "count" => array(
            "counter","idBooth"
        ),
        "serial" => array(
            "serialnumber"
        ),
        "boothDongle" => array(
            "name", "serialnumber"
        )
    );
    
    // Fields
    public $idBooth;
    public $estat;
    public $type;
    public $owner;
    public $name;
    public $obs;
    public $serialnumber;
    public $location;
    public $latitude;
    public $longitude;
    public $lastConnLocal;
    public $lastConn;
    public $timeZone;
    public $lastConnZone;
    public $alertOffline;
    public $hS;
    public $mS;
    public $hE;
    public $mE;
    public $hmS;
    public $hmE;
    public $midnight;
    public $report;
    public $cardReaderSN;
    public $payPalVendor;
    public $version;
    public $CLD_Status;
    public $CLD_date_start_production;
    public $CLD_date_production;
    public $CLD_date_sold;
    public $CLD_date_tOwner;
    public $CLD_ControlQuality;
    public $CLD_Distributor;
    public $CLD_idType;
    public $CLD_subDistributor;
    public $CLD_production;
    public $PBtwid;
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