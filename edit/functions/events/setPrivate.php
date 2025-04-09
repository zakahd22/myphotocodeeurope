<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$private = $_POST['private'];

$baseController = new baseController;
$baseController->createModel('events');

$array = array('private' => $private);
$upd = $baseController->eventsModel->updateEvent($ID, $array); 
    
if($upd){
    echo "OK";    
}
else{
    echo "ERROR";
}
