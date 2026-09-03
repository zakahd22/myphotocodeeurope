<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$value = $_POST['value'];
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('App_boothAlertDef');

$boothAlert = $baseController->App_boothAlertDefModel->getAlerts($ID, 12);

if($boothAlert){
    $array = array('value' => $value);
    $upd = $baseController->App_boothAlertDefModel->updateAlertDef($ID, 12, $array); 
    
    if ($upd) {
        echo "OK";
    } else {
        echo "ERROR";
    }
}
else{
    
    $baseController->entity->loadEntity('App_boothAlertDef');
    $baseController->entity->setValue("idBooth", $ID);
    $baseController->entity->setValue("typeAlert", 12);
    $baseController->entity->setValue("value", $value);

    $insert = $baseController->App_boothAlertDefModel->insertBoothAlertDef();
    
    if ($insert){
        echo "OK";
    } else {
        echo "ERROR";
    } 
}