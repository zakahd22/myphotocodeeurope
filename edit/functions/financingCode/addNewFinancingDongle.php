<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('booths');
$baseController->createModel('Fcode_dongle');

$result = "Ko";

$json = json_decode($_POST["dades"], TRUE);

for($i=0; $i< count($json); $i++){
    if ($json[$i]['name']){
        $array[$json[$i]['name']] = $json[$i]['value'];
    }
}

if($array["dongelString"]){
    $both = $baseController->boothsModel->getBoothsByString($array["dongelString"]);
    
    if($both){
        $idDongle = $both[0]["id"];
    }

    $baseController->entity->loadEntity('Fcode_dongle');
    $baseController->entity->setValue("idDongle", $idDongle);

    $FcodeDongle  = $baseController->Fcode_dongleModel->insertFcodeReg();

    if($FcodeDongle){
        $result = "Ok";
    }
}
echo $result;