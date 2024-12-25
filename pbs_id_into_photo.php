<?php

require_once "common/global.php";
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController;
$baseController->createModel('App_boothDongle');
$baseController->createModel('photos');

utils::log("Trace 1", "logScript");

$limit = $_GET["limit"];
echo $limit;
$dongles = $baseController->App_boothDongleModel->boothDonglescript($limit);

utils::log("Trace 2", "logScript");


foreach ($dongles as $dongle){
    
//    $dongle_id = 1076;
//    $pbs_id    = 7676;
//    $datetimeS = "2016-05-16 10:39:28";
//    $datetimeF = "2016-10-19 12:00:00";

    $dongle_id = $dongle["idDongle"];
    $pbs_id    = $dongle["idBooth"];
    $datetimeS = $dongle["datetimeS"];
    $datetimeF = $dongle["datetimeF"];


    if($datetimeF == NULL) $datetimeF = "2016-10-19 12:00:00";

    $photos = $baseController->photosModel->getPhotosScript($dongle_id, $datetimeS, $datetimeF);
    
    foreach ($photos as $photo){
        $id = $photo["id"];
        $pbs_id_bd = $photo["pbs_id"];
        
        if($pbs_id_bd == 0){
            $updates = array('pbs_id' => $pbs_id);
            $upd = $baseController->photosModel->updatePhotoAsId($id, $updates);
            
            utils::log("----------------><--------------------", "logScript");
            if($upd) utils::log("La Photo  $id  se l'hi ha assigat el pbs: $pbs_id", "logScript");
            else utils::log ("Fail", "logScript");
            utils::log("----------------<>--------------------", "logScript");
        }
        else{
            utils::log("----------------><--------------------", "logScript");
            utils::log ("La Photo $id ja te photobooth_id", "logScript");
            utils::log("----------------<>--------------------", "logScript");
        }
        $i++;
        utils::log($i, "logScript","counter");
    }
}
if($limit<2824){
    $limit += 200;
    header("Location: pbs_id_into_photo.php?limit=$limit");
}
else{
    echo '<br />se fini';
}