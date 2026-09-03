<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$cName= addslashes($_POST['n']);
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('rentals');

$array = array('name' => $cName);
$upd = $baseController->rentalsModel->updateRental($ID, $array);

if($upd){
    echo "OK";
}
else{
    echo "ERROR";
}
