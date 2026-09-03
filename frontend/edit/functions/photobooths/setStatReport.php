<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$value = $_POST['value'];
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('App_boothConfigDef');

$pbConfig = $baseController->App_boothConfigDefModel->getApp_boothConfigDef($ID, 1);

if(count($pbConfig) == 0){$x = true;}
else{$x = false;}

if ($x) {
    $baseController->entity->loadEntity('App_boothConfigDef');
    $baseController->entity->setValue("idBooth", $ID);
    $baseController->entity->setValue("typeConfig", 1);
    $baseController->entity->setValue("value", $value);
    
    $insert = $baseController->App_boothConfigDefModel->insertAppBoothConfigDed();
    utils::log($insert, "test");
    if($insert){
        echo "OK";
    } 
    else {
        echo "ERROR";
    }
} 
else {
    $array = array('value' => $value);
    $upd = $baseController->App_boothConfigDefModel->updAppBoothConfigDef($ID, $array, 1); 
    
    if ($upd) {echo "OK";} 
    else {echo "ERROR";}
}



