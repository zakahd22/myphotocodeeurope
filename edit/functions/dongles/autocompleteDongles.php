<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('booths');

$result = "Error";

$booths = $baseController->boothsModel->getAllBooths();

if($booths){
    $i = 0;
    $result = array();
    foreach ($booths as $booth){
        $result[$i]['value']  = $booth["id"];
        $result[$i]['label']  = $booth["rand_string"];
        $i++; 
    }
}

echo json_encode($result);
