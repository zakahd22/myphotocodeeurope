<?php
require_once "config/ig_config.php";
$image_id = "";

try {

    guardAgainstMissingParameters();

    $word = $_GET['w'];
    $token = $_GET['tkn'];
    $image_id = $_GET['idphoto'];
    $extension = $_GET['ext'];

    $filename = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$word/$image_id.$extension";
//print $filename;exit;
 
    if (!file_exists($filename) || !is_file($filename)) {
        throw new Exception("");
    }

    header("Content-type: image/jpeg");
    if (!readfile($filename)) {
        throw new Exception("");
    }

} catch(Exception $e) {
    
    http_response_code(500);
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        if (!array_key_exists('w', $_GET) || !isset($_GET['w'])) {
            throw new Exception("Missing word");
        }
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("Missing token");
        }       
        if (!array_key_exists('idphoto', $_GET) || !isset($_GET['idphoto'])) {
            throw new Exception("Missing idphoto");
        }
        if (!array_key_exists('ext', $_GET) || !isset($_GET['ext'])) {
            throw new Exception("Missing photo extension");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

