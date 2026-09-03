<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('Fcode_reg');

$json = json_decode($_POST["dades"], TRUE);

$dateEnd = "";

$dongle = $baseController->Fcode_regModel->getDateEndFcodeReg($json);

if($dongle){
    $lastDateEnd = $dongle[0]["dateEnd"];
}

$fecha = new DateTime;
if($lastDateEnd){
    $fecha = new DateTime($lastDateEnd);
}

$fecha = $fecha->modify("+180 day");
$DateEnd = $fecha->format('m/d/Y');

$gracePlays = 15;
// Comprovar que no existeixi el random code. En un principi no es mira.
$code = utils::get_rndm32(5);

// Comprovar que no existeixi el random puk. En un principi no es mira.
$puk = utils::get_rndm32(5);

$result = [$DateEnd, $gracePlays, $code, $puk];
$result = json_encode($result);

echo $result;