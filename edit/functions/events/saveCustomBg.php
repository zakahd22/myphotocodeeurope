<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

function get_file($pattern){
    $result = false;
    $files = glob($pattern . "*");
    if(count($files) == 1){
        $result = $files[0];
    }
    return $result;
}

$baseController = new baseController();
$baseController->createModel('events');

$ID = $_POST['id'];
$dc= $_POST['dc'];

$filename = false; 
$path = "../../../images/ownerIMG/tmp/bg{$ID}";

$filename = get_file($path);
$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

if ($dc == 1) {
    $event = $baseController->eventsModel->getEvent($ID);
    if ($event) {
        $fld = $event[0]["start_date"] . $ID;
        $path2 = "../../../events/{$fld}/background";
    }
    if (file_exists($filename)) {
        if (copy($filename, $path2 . "." . $file_extension)) {
            $updates = array('background_id'=>99);
            if($baseController->eventsModel->updateEvent($ID, $updates)){
                unlink($filename);
                echo "OK";
            }
        } else {
            echo "Can not save this image, try again. If this problem persists contact us at main@myphotocode.com.";
        }
    } else {
        echo "Can not save this image, try again. If this problem persists contact us at main@myphotocode.com.";
    }
} else {
    $bgId = $_POST['bg'];
    $updates = array('background_id'=>$bgId);
    if($baseController->eventsModel->updateEvent($ID, $updates)){
        echo "OK";
    }else{
        echo "Error has ocurred, try again. If this problem persists contact us at main@myphotocode.com.";
    }
}
?>
