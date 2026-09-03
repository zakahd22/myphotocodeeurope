<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class CLD_banners{
    // Table Name
    static public $TableName = "CLD_banners";
    
    // Data sets
    public $aDatasets = array (
        "all" => array(
            "id", "banner", "rental_id", "banner_url"
        ),
        "CloudBanner" => array(
            "banner", "banner_url"
        )
    );
    
    // Fields
    public $id;
    public $banner;
    public $rental_id;
    public $banner_url;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}