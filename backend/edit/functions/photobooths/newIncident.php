<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController;
$baseController->createModel('App_booths');
$baseController->createModel('CLD_Login');
$baseController->createModel('CLD_Incidents');
$baseController->createModel('CLD_historyBooth');

$coment = addslashes($_POST['coment']);
$ID = $_POST['id'];
$dateTime = date("Y-m-d H:i:s");
$uID = $_SESSION['USERID'];
$uT = $_SESSION['USERTYPE'];

$pbs = $baseController->App_boothsModel->getBoothWhereid($ID);

if($pbs){
    $sn = $pbs[0]["serialnumber"];
}

$login = $baseController->CLD_LoginModel->getLoginWhereUserIdUserType($uID, $uT);

if($login){
    $username = $login[0]["username"];
}

$code = "#userin";
$status = 0;

$baseController->entity->loadEntity('CLD_Incidents');
$baseController->entity->setValue("idBooth", $ID);
$baseController->entity->setValue("coment", $coment);
$baseController->entity->setValue("datetime", $dateTime);
$baseController->entity->setValue("code", $code);
$baseController->entity->setValue("user", $username);
$baseController->entity->setValue("status", $status);

$insert = $baseController->CLD_IncidentsModel->insertIncidents();

if($insert > 0){
    $coment = addslashes("Added new incident -$in-");
    
    $baseController->entity->loadEntity('CLD_historyBooth');
    $baseController->entity->setValue("comment", $coment);
    $baseController->entity->setValue("data", $dateTime);
    $baseController->entity->setValue("idBooth", '');
    $baseController->entity->setValue("sn", $sn);

    $insert = $baseController->CLD_historyBoothModel->insertCLD_historyBooth();
    
    echo "OK";
}
else{
    echo "ERROR";
}
