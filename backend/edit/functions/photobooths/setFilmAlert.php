<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$status = "ERROR";
$message = "Unknown error";
$isTurnedOff = true;

$value = $_POST['value'];
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('App_boothAlertDef');

$boothAlertDef = $baseController->App_boothAlertDefModel->getAlerts($ID, 11);

if(count($boothAlertDef) != 0){
    if($boothAlertDef[0]['value'] != "none"){
        $isTurnedOff = false;
    }
}

if(!$isTurnedOff || ($isTurnedOff && $value != "none")){
    if(count($boothAlertDef) == 0){
        $baseController->entity->loadEntity('App_boothAlertDef');
        $baseController->entity->setValue("idBooth", $ID);
        $baseController->entity->setValue("typeAlert", 11);
        $baseController->entity->setValue("value", $value);

        $insert = $baseController->App_boothAlertDefModel->insertBoothAlertDef();

        if($insert){
            $status = "OK";
        }
    }
    else {
        $array = array('value' => $value);
        $upd = $baseController->App_boothAlertDefModel->updateAlertDef($ID, 11, $array); 

        if($upd){
            $status = "OK";
        }
    }
}
else {
    $message = "First select a unit value";
}

$result = json_encode(array('status'=>$status, 'message'=>$message));
echo $result;
