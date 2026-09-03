<?php

/**
 * Event entity, Object representing an event
 */
class trashed_photo_Files {
    // Table Name
    static public $TableName = "photo_Files";
    
    // Data sets
    public $aDatasets = array (
        "all" => array (
            "id", "photoId", "ServerId", "name", "path", "fileType", "fileSize", "photobooth", "dongle", "date"
        ),
        "count" => array (
            "counter","id"
        ),
    );
    
    // Fields
    public $id;
    public $photoId;
    public $ServerId;
    public $name;
    public $path;
    public $fileType;
    public $fileSize;
    public $photobooth;
    public $dongle;
    public $date;
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