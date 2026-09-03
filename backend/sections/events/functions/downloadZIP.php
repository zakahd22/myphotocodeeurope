<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$folder = $_POST['folder'];
$eventID = $_POST['eventID'];


$directoryToZip = G_PATH."usbs/" . $folder . "/";
$directoryToZip2 = G_PATH."printPhoto/e$eventID/";
$zipFileName = $directoryToZip . "save-in-usb.zip";
if (file_exists($zipFileName)){
    unlink($zipFileName);
}
ini_set("max_execution_time", 300);

$zip = new ZipArchive();
if ($zip->open($zipFileName, ZIPARCHIVE::CREATE) !== TRUE) {
    echo "ERROR";
}

$error = "";
$x = true;
try {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryToZip));
    foreach ($iterator as $key => $value) {
        $keyZip = str_replace($directoryToZip, "", $key);
        $keyZip2 = substr($keyZip, -1);
        
        if ($keyZip2 != "." && $keyZip2 != ".." && $keyZip2 != "") {
            $zip->addFile(realpath($key), $keyZip) or die("$F22" . $keyZip);
        }
    }
    if (file_exists($directoryToZip2)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryToZip2));
        foreach ($iterator as $key => $value) {

            $keyZip = str_replace($directoryToZip2, "", $key);
            $keyZip2 = substr($keyZip, -1);
            if ($keyZip2 != "." && $keyZip2 != ".." && $keyZip2 != "") {
                $zip->addFile(realpath($key), $keyZip) or die("$F22" . $keyZip);
            }
            
        }
    }
}catch(Exception $e){
     $error =  $e->getMessage();
     $x = false;
}

$zip->close();

$array_result = array();
if($x){
    //echo G_PAGE . "usbs/$folder/save-in-usb.zip";
    $url = G_PAGE . "usbs/$folder/save-in-usb.zip";
    $array_result = array('success' => true, 'url' => $url, 'error' => $error);
    
    $files = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Frames/*"); // get all file names
        foreach ($files as $fr) { // iterate files
        unlink($fr);
    }
}
else {
    //echo "ERROR";
    $array_result = array("success" => false, "url" => false, "error" => $error);
}

echo json_encode($array_result);