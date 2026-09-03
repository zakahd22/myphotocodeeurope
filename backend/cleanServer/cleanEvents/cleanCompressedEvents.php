<?php
/* Aquesta funcio el que fa es agafa totes les fotos que han estat tretes del arxiu comprimit de un event i han estat eliminades. **/
echo "Deleting Compressed Photos \n";

$baseController = new baseController;
$baseController->createModel('photos');
$baseController->createModel('events');

$photos = $baseController->photosModel->getPhotosByCLD_pDelete();

if($photos){
    foreach ($photos as $photo){
        $code = $photo["code"];
        $eventID = $photo["event_id"];
        $event = $baseController->eventsModel->getEvent($eventID);
        
        if($event){
            
            $startDate = $event[0]["start_date"];
            $folder =  G_PATH . "events/" . $startDate . $eventID . "/";
            $zip_folder = G_PATH . "events/compressed_events/".$eventID."_compressed.zip";
                $zip = new ZipArchive();
                echo $zip_folder;
                if($zip->open($zip_folder) === TRUE ){
//                    echo $zip;
                    $archiu = '/'.$code.'.jpg';
                    if ($zip->locateName($archiu) !== false){
                        echo "Config exists";
                        echo "Esborrant photo -- " . $code . "... \n";
                        utils::log("Esborrant photo -- {$code}...", "logDeleteCompressedPhotos");
                        $removed = deleteCompressedPhotos($folder , $code);
                        utils::log("S'han esborrat {$removed} fitxers", "logDeleteCompressedPhotos");
                        echo "S'han esborrat " . $removed . " fitxers \n";
                        if($removed){
                            $updates = array('CLD_pDelete' => 0);
                            $baseController->photosModel->updatePhoto($code, $updates);
                        }
                        else{
                            $updates = array('CLD_pDelete' => 0);
                            $baseController->photosModel->updatePhoto($code, $updates);
                        }
                    }
                    else{
                        echo $archiu;
                    }
                }
                else {
                    echo 'Failed code:'. $code;
                }     
        }        
    }
}








//      