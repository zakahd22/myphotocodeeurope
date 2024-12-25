<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$loc = $_POST['loc'];
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('App_booths');

$array = array('location' => $loc);
$upd = $baseController->App_boothsModel->updateAppBooths($ID, $array); 

if($upd) echo "OK";
else echo "ERROR";

