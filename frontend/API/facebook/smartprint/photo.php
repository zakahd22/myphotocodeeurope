<?php
require_once "config/fb_config.php";
$image_id = "";

try {
    guardAgainstMissingParameters();
    $token = $_GET['tkn'];
    $image_id = $_GET['id'];
    $extension = $_GET['ext'];

    $filename = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$image_id.$extension";

    if (!file_exists($filename) || !is_file($filename)) {
        throw new Exception("");
    }
    header("Content-type: image/jpeg");
    if (!readfile($filename)) {
        throw new Exception("");
    }
} catch(Exception $e) {
    utils::log("photo error: ". $e->getMessage(), LOG_FILE);
    http_response_code(500);
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("Missing token");
        }
        if (!array_key_exists('id', $_GET) || !isset($_GET['id'])) {
            throw new Exception("Missing extension");
        }
        if (!array_key_exists('ext', $_GET) || !isset($_GET['ext'])) {
            throw new Exception("Missing photo id");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

