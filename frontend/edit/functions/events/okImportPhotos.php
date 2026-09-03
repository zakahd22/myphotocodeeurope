<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$fromEvent = $_POST['eventFROM'];
$toEvent = $_POST['eventTO'];

$baseController = new baseController;
$baseController->createModel('events');
$baseController->createModel('photos');

$event = $baseController->eventsModel->getEvent($fromEvent);
if($event){
    $fromFolder = $event[0][start_date] . $fromEvent;
}
$event = $baseController->eventsModel->getEvent($toEvent);
if($event){
    $toFolder = $event[0][start_date] . $toEvent;
}
//$CLD_CON->OpenRs("SELECT start_date FROM events WHERE id=$fromEvent");
//while($CLD_CON->FetchArray()){
//    $fromFolder = $CLD_CON->GetArrayField("start_date") . $fromEvent;
//}
//$CLD_CON->OpenRs("SELECT start_date FROM events WHERE id=$toEvent");
//while($CLD_CON->FetchArray()){
//    $toFolder = $CLD_CON->GetArrayField("start_date") . $toEvent;
//}

$p="";
$x=true;


$photos = $baseController->photosModel->getPhotos($fromEvent);
if($photos){
    foreach ($photos as $photo){
        $code = $photo["code"];
        if(isset($_POST["$code"])){
            if(file_exists(G_PATH . "events/$fromFolder/$code.jpg")){
                if(rename(G_PATH . "events/$fromFolder/$code.jpg" , G_PATH . "events/$toFolder/$code.jpg" )){
                    $array = array('event_id' => $toEvent);
                    $upd = $baseController->photosModel->updatePhotoByEvent($fromEvent, $array, $code);
                    if(!$upd){
                        $x= false;
                        $p .= "$code , ";
                    } 
                }
                else {
                    $x = false;
                }
            }
            if(file_exists(G_PATH . "events/$fromFolder/$code.wmv")){
                if(rename(G_PATH . "events/$fromFolder/$code.wmv" , G_PATH . "events/$toFolder/$code.wmv" )){
                    
                }
                else {
                    $x = false;
                }
            }
            if(file_exists(G_PATH . "/events/$fromFolder/$code.mp4")){
                if (rename(G_PATH . "events/$fromFolder/$code.mp4" , G_PATH . "events/$toFolder/$code.mp4" )){
                    
                }
                else {
                    $x = false;
                }
            }
        }
    }
    if($x){
        echo "OK";
    }
    else{
        echo "ERROR, have error with photos $p";
    }
}

//$CLD_CON->OpenRs("SELECT code FROM photos WHERE event_id = $fromEvent");
//while($CLD_CON->FetchArray()){
//    $code = $CLD_CON->GetArrayField("code");
//    if(isset($_POST["$code"])){
//        if(file_exists(G_PATH . "events/$fromFolder/$code.jpg")){
//            if(rename(G_PATH . "events/$fromFolder/$code.jpg" , G_PATH . "events/$toFolder/$code.jpg" )){
//                if($CLD_CON->Execute("UPDATE photos SET event_id=$toEvent WHERE code='$code' AND event_id=$fromEvent")){}
//                else{
//                    $x= false;
//                    $p .= "$code , ";
//                }
//            }
//        }
//        if(file_exists(G_PATH . "events/$fromFolder/$code.wmv")){
//            if(rename(G_PATH . "events/$fromFolder/$code.wmv" , G_PATH . "events/$toFolder/$code.wmv" )){}
//        }
//        if(file_exists(G_PATH . "/events/$fromFolder/$code.mp4")){
//            if(rename(G_PATH . "events/$fromFolder/$code.mp4" , G_PATH . "events/$toFolder/$code.mp4" )){}
//        }
//    }
//}
//
//if($x){
//    echo "OK";
//}
//else{
//    echo "ERROR, have error with photos $p";
//}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
