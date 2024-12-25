<?php

include '../../../sessio.php';
require_once "../../../common/global.php";

$baseController = new baseController;
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('App_booths');
$baseController->createModel('CLD_historyBooth');

$sn = $_POST['sn'];
$ID = $_POST['id'];
$date= date("Y-m-d H:i:s");


if (strlen($sn) != 13) {
    echo "ERROR , el serialnumber esta composat de 13 numeros( ex : 0034000340000 )";
} else {
    $type = substr($sn, 2, 2);
    
    $CLD_boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeByModel($type);
    if($CLD_boothTypes){
        $idType = $CLD_boothTypes[0]["id"];
        
        $array = array('serialnumber' => $sn, 'CLD_idType' => $idType);
        $upd = $baseController->App_boothsModel->updateAppBooths($ID, $array); 
        
        if($upd){
            echo "OK";
            $coment = addslashes("Change SN to $sn");
            
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
    }
    else{
        echo "ERROR , El numero $type no correspon a cap model.";
    }
}
