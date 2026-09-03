<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('Fcode_reg');

$id         = "";
$dateEnd    = "";
$gracePlays = "";
$code       = "";
$puk        = "";

$json = json_decode($_POST["dades"], TRUE);

$result = "Error01";

$id         = $json[0];
$dateEnd    = $json[1];
$gracePlays = $json[2];
$code       = $json[3];
$puk        = $json[4];

if($dateEnd){
    $dateEnd = utils::date_std_to_datetime($dateEnd, 'Y-m-d', 'm/d/Y');
}

$updates = array('dateEnd' => $dateEnd, 'gracePlays' => $gracePlays, 'code' => $code, 'puk' => $puk);
$upd = $baseController->Fcode_regModel->updateFcodeReg($id, $updates);

    
if($upd){
    $result = TRUE;
}

echo $result;