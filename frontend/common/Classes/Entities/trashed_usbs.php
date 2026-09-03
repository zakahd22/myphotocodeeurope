<?php

/**
 * Event entity, Object representing an event
 */
class trashed_usbs {
    // Table Name
    static public $TableName = "usbs";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "rental_id", "creation_date", "title", "boothtype_char", "event_id", "logo","text", "bgmusic",
            "frame1", "frame2", "frame3", "frame4", "frame5", "frame6", "frame7", "frame8", "frame9", "frame10", "frame11", "frame12",
            "welcome_type", "welcome", "welcome1", "welcome2", "welcome3", "welcome4", "welcome5", "welcome6", "welcome7", "welcome8",
            "welcome9", "welcome10", "bye_type", "bye", "bye1", "bye2", "bye3", "bye4", "bye5", "bye6", "bye7", "bye8", "bye9",
            "bye10", "banner", "custom1", "custom2", "custom3", "custom4", "custom5", "custom6", "custom7", "custom8", "custom9",
            "custom10", "custom11", "custom12", "available", "CLD_IdTypeBooth"
        )
    );
    
    // Fields
    public $id;
    public $rental_id;
    public $creation_date;
    public $title;
    public $boothtype_char;
    public $event_id;
    public $logo;
    public $text;
    public $bgmusic;
    public $frame1;
    public $frame2;
    public $frame3;
    public $frame4;
    public $frame5;
    public $frame6;
    public $frame7;
    public $frame8;
    public $frame9;
    public $frame10;
    public $frame11;
    public $frame12;
    public $welcome_type;
    public $welcome;
    public $welcome1;
    public $welcome2;
    public $welcome3;
    public $welcome4;
    public $welcome5;
    public $welcome6;
    public $welcome7;
    public $welcome8;
    public $welcome9;
    public $welcome10;
    public $bye_type;
    public $bye;
    public $bye1;
    public $bye2;
    public $bye3;
    public $bye4;
    public $bye5;
    public $bye6;
    public $bye7;
    public $bye8;
    public $bye9;
    public $bye10;
    public $banner;
    public $custom1;
    public $custom2;
    public $custom3;
    public $custom4;
    public $custom5;
    public $custom6;
    public $custom7;
    public $custom8;
    public $custom9;
    public $custom10;
    public $custom11;
    public $custom12;
    public $available;
    public $CLD_IdTypeBooth;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}