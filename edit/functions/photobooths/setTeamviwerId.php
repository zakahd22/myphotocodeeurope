<?php

include '../../../sessio.php';
require_once "../../../common/global.php";

$baseController = new baseController;
$baseController->createModel('App_booths');
$baseController->createModel('CLD_historyBooth');

$twid   = $_POST['tw'];
$ID     = $_POST['id'];
$date   = date("Y-m-d H:i:s");


$array = array('PBtwid' => $twid);
$upd = $baseController->App_boothsModel->updateAppBooths($ID, $array); 

if($upd){
    echo "OK";
    $coment = addslashes("Change TeamviweID to $twid");

    $baseController->entity->loadEntity('CLD_historyBooth');
    $baseController->entity->setValue("comment", $coment);
    $baseController->entity->setValue("data", $date);
    $baseController->entity->setValue("idBooth", $ID);
    $baseController->entity->setValue("sn", $sn);
    $baseController->CLD_historyBoothModel->insertCLD_historyBooth();
} 
else {
    echo "ERROR";
}
