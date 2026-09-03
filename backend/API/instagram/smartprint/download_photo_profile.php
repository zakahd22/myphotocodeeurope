<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use instagram\smartprint\infrastructure\PDOInstagramRepository;

require_once "infrastructure/PDOInstagramRepository.php";
require_once "config/ig_config.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/global.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/conexio.php';

// require_once 'config/ig_config.php';
require_once '../../../common/global.php';
require_once '../../../common/conexio.php';
// require_once __DIR__ . '/../../../vendor/autoload.php'; // change path as needed

try {   
    $igRepository = new PDOInstagramRepository();    
     
    guardAgainstMissingParameters();
   
    $fbid = getInputFbid();
  
    
    //tot aquest xou per recuperar la url de la foto origen a IG sense pasar-la per get
    $filename = $_SERVER["DOCUMENT_ROOT"] . IMAGES_PATH . "/{$fbid}/{$fbid}.txt";
    if (!file_exists($filename)) {
        throw new Exception("download_photo_profile: missing $filename");
    }
    $file_content = @file($filename);   

    $file_explode = explode("|",$file_content[0]);
    $photo = str_replace(PHP_EOL, '', $file_explode[0]);
    if(isset($photo)) {
        utils::log("id_photo profile: $photo at: {$fbid}", LOG_FILE);
        $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$fbid}/$fbid.jpg";
        utils::log("download_photo_profile: img: $img", LOG_FILE);
        downloadIGPhoto($img, $photo);

    }       
  
   
} catch(Exception $e){    
    utils::log("download_photos: ". getInputToken(). " error: {$e->getMessage()}", LOG_FILE);
    echo $e->getMessage();
}



function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        if (empty(getInputToken())) {
            utils::log("Download error: Missing token", LOG_FILE);
            throw new Exception("{\"status\": \"KO\", \"message\":\"Missing token\"}");
        }
        if (empty(getInputFbid())) {
            utils::log("Download error: Missing fbid", LOG_FILE);
            throw new Exception("{\"status\": \"KO\", \"message\":\"Missing fbid\"}");
        }
        
    } else {
        throw new Exception("Not a GET request");
    }
}

function getInputToken(){
    if (array_key_exists('tkn', $_GET) && !empty($_GET['tkn'])) {
        return $_GET['tkn'];
    }
    return "";
}

function getInputFbid(){
    if (array_key_exists('fbid', $_GET) && !empty($_GET['fbid'])) {
        return $_GET['fbid'];
    }
    return "";
}



function downloadIGPhoto($final_image, $photo_url){
    $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_URL, $photo_url);
    $result=curl_exec($ch);
    file_put_contents($final_image, $result);
}



