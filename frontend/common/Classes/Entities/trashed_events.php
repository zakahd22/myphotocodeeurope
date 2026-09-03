<?php

/**
 * Event entity, Object representing an event
 */
class trashed_events {
    // Table Name
    static public $TableName = "events";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "rental_id", "start_date", "title", "background_id", "CLD_banner", "CLD_banner_URL", "private", 
            "autocreated", "ftp_folder_id", "available", "CLD_invitedName", "CLD_invitedEmail", "CLD_SecurityCode", 
            "CLD_eventManegerId", "CLD_date_lastPhoto", "hashtag", "checked", "compressed", "trashed", "newServer"
        ),
        "lookPhotos" => array (
            "id", "start_date", "title", "background_id", "CLD_banner", "CLD_banner_URL", "private", "available", "CLD_invitedName", "CLD_invitedEmail", "CLD_SecurityCode", 
            "CLD_eventManegerId", "CLD_date_lastPhoto", "hashtag", "checked", "compressed", "trashed", "newServer"
        ),
        "count" => array (
            "counter","id", "rental_id", "start_date", "title", "background_id", "CLD_banner", "CLD_banner_URL", "private", 
            "autocreated", "ftp_folder_id", "available", "CLD_invitedName", "CLD_invitedEmail", "CLD_SecurityCode", 
            "CLD_eventManegerId", "CLD_date_lastPhoto", "hashtag", "checked", "compressed", "trashed", "newServer"
        ),
        "sendPhoto" => array (
            "hashtag", "title"
        )
    );
    
    // Fields
    public $id;
    public $rental_id;
    public $start_date;
    public $title;
    public $background_id;
    public $CLD_banner;
    public $CLD_banner_URL;
    public $private;
    public $autocreated;
    public $ftp_folder_id;
    public $available;
    public $CLD_invitedName;
    public $CLD_invitedEmail;
    public $CLD_SecurityCode;
    public $CLD_eventManegerId;
    public $CLD_date_lastPhoto;
    public $hashtag;
    public $checked;
    public $compressed;
    public $trashed;
    public $counter;
    public $newServer;
    
    public function __construct() {
        $this->clearEntity();
    }
    
    public function clearEntity() {
        foreach ($this->aDatasets['all'] as $entityField){
            $this->{$entityField} = null;
        }
    }
}