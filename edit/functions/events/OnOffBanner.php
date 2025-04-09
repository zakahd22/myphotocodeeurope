<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');

$ID = $_POST['id'];
$onOff = $_POST['onoff'];
$event = $baseController->eventsModel->getEvent($ID);
if ($event) {
    $d = $event[0]["start_date"];
    $fld = $d . $ID;
}
$updates = array('CLD_banner'=>$onOff);

if($baseController->eventsModel->updateEvent($ID, $updates)){
    if($onOff == 0){
        unlink(G_PATH . "events/{$fld}/banner.jpg");
        unlink(G_PATH . "events/{$fld}/banner.JPG");
        unlink(G_PATH . "events/{$fld}/banner.jpeg");
        unlink(G_PATH . "events/{$fld}/banner.JPEG");
        unlink(G_PATH . "events/{$fld}/banner.gif");
        unlink(G_PATH . "events/{$fld}/banner.GIF");
    }
    echo "OK";
}else{
    echo "ERROR";
}
?>
