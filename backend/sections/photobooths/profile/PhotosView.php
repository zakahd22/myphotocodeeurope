<?php

class photosController extends baseController{
    private $event_id;
    
    private $user_id;
    private $photo_id;
    private $photo_code;
    private $photo_date;
    private $event_id;
    private $event_name;
    private $datePhoto;
            
    public function __construct() {
        
    }
    
    public function getLookPhotos($code){
        $this->createModel('App_boothDongle');
        $this->createModel('events');
        $this->createModel('photos');
    }
}