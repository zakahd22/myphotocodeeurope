<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$ID = $_POST['id'];
$hash = $_POST['hashtag'];

$updates = array('hashtag'=>$hash);
if ($baseController->eventsModel->updateEvent($ID, $updates)){
    echo "OK";
} else {
    echo "ERROR";
}


?>