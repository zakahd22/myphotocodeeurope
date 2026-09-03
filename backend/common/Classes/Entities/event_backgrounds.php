<?php

/**
 * rentals entity, Object representing a rental(owner)
 */
class event_backgrounds{
    // Table Name
    static public $TableName = "event_backgrounds";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "title", "image_url", "align_x", "align_y", "color", "repeat", "rental_id"
        )
    );
    
    // Fields
    public $id;
    public $title;
    public $image_url;
    public $align_x;
    public $align_y;
    public $color;
    public $repeat;
    public $rental_id;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}