<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('Fcode_dongle');

$json = json_decode($_POST["dades"], TRUE);

for($i=0; $i< count($json); $i++){
    if ($json[$i]['name']){
        $array[$json[$i]['name']] = $json[$i]['value'];
    }
}

$allowTest = 0;
if($array["allowTest"]){
    $allowTest = 1;
}

$result = "Error";

$updates = array('codeAct' => $array["codeAct"], 'codeReset' => $array["codeReset"], 'allowTest' => $allowTest, 'idPB' => $array["idPb"]);

$upd = $baseController->Fcode_dongleModel->updateFinancingDongle($array['dongle'], $updates);
  
if($upd){
    $result = TRUE;
}

echo $result;