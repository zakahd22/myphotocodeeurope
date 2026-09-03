<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$paypal = $_POST['paypal'];

$baseController = new baseController;
$baseController->createModel('App_booths');

$array = array('payPalVendor' => $paypal);
$upd = $baseController->App_boothsModel->updateAppBooths($ID, $array); 


if($upd){
    echo "OK";
}
else{
    echo "ERROR";
}
