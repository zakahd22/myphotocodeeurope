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
    
    /**
     * @param String $context The context where the method is called from (events or photobooths)
     * @param type $id
     */
    public function getPhotosVideosView($context, $id){
        if(method_exists("photosController", "getPhotos_{$context}")){ 
            $context_id = $this->getPhotos_{$context}($id);
            $photos = $this->photosModel->getPhotos($context, $context_id); //getAllFromPhotos

            //do stuff here
            
            $this->createView('photos', 'photosVideos');
            $photosVideosView->getView($photos);
        }
    }
    
    public function getListView($idUser){
        //do stuff here
        
        $this->createView('photos', 'list', $photos);        
    }
    
    
    /**
     * Pre catching valuies to request a photo from a PB id
     * Funcio on definirem totes les variables per poder cirdar la funcio getAllPhotos
     * 
     * @param type $idPB
     * @return Integer Dongle Id
     */
    public function getPhotos_photobooths($idPB){
        $i=0;
        $boothDongles = $baseController->App_boothDongleModel->boothDongles($idPB);
        foreach ($boothDongles as $boothDongle){
            $array[0] = $boothDongle["idDongle"];
            $array[1] = $boothDongle["datetimeS"];
            if(!empty($boothDongle["datetimeF"])){$array[2] = $boothDongle["datetimeF"];}
            else{$array[2] = "3000-01-01";}
            
            $dongles_ids[$i] = $array;
            $i++;
        }
        foreach ($dongles_ids as $arrDongle) {
            $request = $baseController->photosModel->getAllPhotosFromPbs($arrDongle[0], $arrDongle[1], $dateF, $user_id = false);
            $i=0;
            foreach ($request as $photo){
                $photocode = $photo["code"];
                $event = $photo["event_id"];
                $datePhoto = $photo["Appusr_datetime"];

                $photos[$datePhoto] = [$photocode, $event, $datePhoto];    

                $ph[$i]= $photos[$datePhoto];
                $i++;
            }
        }
        krsort($ph);
        $_SESSION['ph'] = $ph;
    }
    
    /**
     * Pre catching valuies to request a photo from an event id
     * 
     * @param type $idEvent
     * @return Integer Event Id
     */
    public function getPhotos_events($idEvent){
        return $idEvent;
    }
}
