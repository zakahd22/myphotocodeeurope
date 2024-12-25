<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('App_boothDongle');
$baseController->createModel('booths');

$result = "Ko";

$strDongle = $_POST["dongleStr"];
$idPb = $_POST["idPb"];

$dongle = $baseController->boothsModel->getBoothsByString($strDongle);

if(is_array($dongle) && !empty($dongle)){
    
    $idDongle = $dongle[0]["id"];
    
    $myDate = new DateTime();
    $startDate = $myDate->format("Y-m-d H:i:s");
    
    $myDate->modify("-1 minutes");
    $forcedDate = $myDate->format("Y-m-d H:i:s");
            
    $array = array('datetimeF' => $forcedDate);
    $upd = $baseController->App_boothDongleModel->updPairingsNotFinish($idPb, $array);
    /* Comprovar resultat de upd. Que pasa si amb el select intern que fa el upd no te registres? que retorna?*/ 
    //if($upd){
    //    $result = "Ok";
    //}
    $baseController->entity->loadEntity('App_boothDongle');
    $baseController->entity->setValue("idBooth", $idPb);
    $baseController->entity->setValue("idDongle", $idDongle);
    $baseController->entity->setValue("datetimeS", $startDate);
    $pairnig = $baseController->App_boothDongleModel->insertPairing(); 
    
    $result = "Ok";
}

echo $result;
   
    
