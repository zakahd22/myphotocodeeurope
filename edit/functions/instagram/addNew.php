<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('Fcode_reg');


$json = json_decode($_POST["dades"], TRUE);

$idDongle   = $json[0];
$dateEnd    = $json[1];
$gracePlays = $json[2];
$code       = $json[3];
$puk        = $json[4];

if($dateEnd){
    $dateEnd = utils::date_std_to_datetime($dateEnd, 'Y-m-d', 'm/d/Y');
}

$result = "Ko";

$baseController->entity->loadEntity('Fcode_reg');
$baseController->entity->setValue("idDongle", $idDongle);
$baseController->entity->setValue("dateEnd", $dateEnd);
$baseController->entity->setValue("gracePlays",$gracePlays);
$baseController->entity->setValue("code", $code);
$baseController->entity->setValue("puk", $puk);
$idReg = $baseController->Fcode_regModel->insertFcodeReg();

if($idReg){
    $result = "Ok";
}

echo $result;