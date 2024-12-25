<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
if(!empty($_POST['lat']) || !empty($_POST['long'])){
$lat = $_POST['lat'] * 1000000;
$lon = $_POST['long'] * 1000000;
}
else{
    $lat="NULL";
    $lon="NULL";
}
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('App_booths');

$array = array('latitude' => $lat, 'longitude' => $lon);
$upd = $baseController->App_boothsModel->updateAppBooths($ID, $array); 

if($upd) echo "OK";
else echo "ERROR";
