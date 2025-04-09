<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('usbs');

$CLD_CON2 = clone($CLD_CON);
$ID = $_POST['id'];

$f = G_PATH . $folder2_ . "printPhoto/e$ID/";
if (file_exists($f)) {
    recursiveRmDir($f);
}

$stardates = $baseController->eventsModel->getEvent($ID);
foreach($stardates as $stardate){
    $eventDate = $stardate["start_date"];
    $f = G_PATH . "/events/" . $eventDate . $ID . "/";
    if(file_exists($f)){
        recursiveRmDir($f);
    }
}

if ($baseController->eventsModel->deleteEvent($ID)) {
    echo "OK";
} else {
    echo "Error";
}

$usbsEvents = $baseController->usbsModel->get_usbsEventId($ID);
foreach($usbsEvents as $usbs){
    $d = $usbs["creation_date"];
    $i = $usbs["id"];
    $f = G_PATH . "usbs/" . $d . $i . "/";
    recursiveRmDir($f);
}

if($baseController->usbsModel->delete_usbsEventID($ID)){
    //OK
} else {
    //Not exist or error
}

function recursiveRmDir($dir) {
    if(isset($dir) && is_dir($dir)){
        $iterator = new RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($filename);
            } else {
                unlink($filename);
            }
        }
    }
}
?>