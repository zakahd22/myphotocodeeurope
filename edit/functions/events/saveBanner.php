<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController;
$baseController->createModel('events');

$ID = $_POST['id'];
$bnImg = $_POST['bn'];
$link= $_POST['link'];
$fld = false;

$filename = explode('?', $bnImg);
$filename = $filename[0];
$ext = pathinfo($filename, PATHINFO_EXTENSION);

$event = $baseController->eventsModel->getEvent($ID);
if ($event) {
    $d = $event[0]["start_date"];
    $fld = $d . $ID;
    $existBanner = $event[0]["CLD_banner"];
}

if($bnImg == "" && $link != ""){
    $array = array('CLD_banner_URL' => $link);
    $upd = $baseController->eventsModel->updateEvent($ID, $array);
    
    if($upd){
        echo "OK";
    }
}

else{
    if (file_exists(G_PATH . "images/ownerIMG/tmp/{$filename}")){
        if($existBanner == 1){
            unlink(G_PATH . "events/{$fld}/banner.jpg");
            unlink(G_PATH . "events/{$fld}/banner.JPG");
            unlink(G_PATH . "events/{$fld}/banner.jpeg");
            unlink(G_PATH . "events/{$fld}/banner.JPEG");
            unlink(G_PATH . "events/{$fld}/banner.gif");
            unlink(G_PATH . "events/{$fld}/banner.GIF");
        }
        if (copy(G_PATH . "images/ownerIMG/tmp/{$filename}" , G_PATH . "events/{$fld}/banner.{$ext}")) {
            $array = array('CLD_banner' => 1, 'CLD_banner_URL' => $link);
            $upd = $baseController->eventsModel->updateEvent($ID, $array); 

            unlink(G_PATH . "images/ownerIMG/tmp/{$filename}");
            echo "OK";
        } 
        else {
            echo "Copy Error\n";
            echo G_PATH . "images/ownerIMG/tmp/{$filename} , " . G_PATH . "events/{$fld}/banner.{$ext}\n";
            echo "Can not save this image, try again. If this problem persists contact us at main@myphotocode.com.";
        }
    } 
    else {
        echo "File Exists Error\n";
        echo "Can not save this image, try again. If this problem persists contact us at main@myphotocode.com.";
    }
}

