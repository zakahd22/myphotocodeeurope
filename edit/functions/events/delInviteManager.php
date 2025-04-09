<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$ID = $_POST['id'];
$result = false;

$updates = array(
    "CLD_invitedName" => NULL,
    "CLD_invitedEmail" => NULL,
    "CLD_SecurityCode" => NULL,
    "CLD_eventManegerId" => NULL
);

if($baseController->eventsModel->updateEvent($ID, $updates)){
    $result = true;
}

echo ($result? "OK":"ERROR");
?>