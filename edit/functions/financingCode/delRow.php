<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('Fcode_reg');

$json = json_decode($_POST["dades"], TRUE);

$result = "Ko";

$del = $baseController->Fcode_regModel->delFcodeReg($json);
    
if($del){
    $result = "Ok";
}

echo $result;