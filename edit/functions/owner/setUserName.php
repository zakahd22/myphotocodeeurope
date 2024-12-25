<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$uName= addslashes($_POST['n']);
$ID = $_POST['id'];

$baseController = new baseController;
$baseController->createModel('rentals');
$baseController->createModel('CLD_Login');

$array = array('username' => $uName);
$upd = $baseController->rentalsModel->updateRental($ID, $array);

if($upd){
    $array = array('username' => $uName);
    $upd_1 = $baseController->CLD_LoginModel->updateLogin($ID, 4, $array);

    if($upd_1){
        echo "OK";
    }
    else{
      echo "ERROR";     
    }
}
else{
    echo "ERROR";    
}
