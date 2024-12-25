<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use facebook\smartprint\domain\FacebookUser;
use facebook\smartprint\infrastructure\PDOFacebookRepository;

require_once "infrastructure/PDOFacebookRepository.php";
require_once "domain/FacebookUser.php";
require_once 'config/fb_config.php';
require_once '../../../common/global.php';
require_once __DIR__ . '/../../../vendor/autoload.php'; // change path as needed

try {
    $fbRepository = new PDOFacebookRepository();
    guardAgainstMissingParameters();
    $fbUser = getFBUser($_GET['token']);
    if(!isset($fbUser)){
        utils::log("Download error: user token not found", LOG_FILE);
        throw new Exception("User token not found: {$input['token']}");
    }
    if($fbUser->getAllPhotosServed() == 1) {
        throw new Exception("All photos served!");;
    }
    $fb = createFBClient();
    $downloaded_photos = 0;
    $file_content = getPhotoIds($fbUser);
    $num_photos_in_file = count($file_content);
    utils::log("download_photos: num_photos_in_file: $num_photos_in_file", LOG_FILE);

    $index = searchLastPhotoIndex($file_content, $fbUser->getLastPhoto());
    utils::log("download_photos: num_photos_in_file: {$fbUser->getLastDownloadedPhotos()} - {$fbUser->getLastDownloadedPhotos()} - {$fbUser->getNumPhotos()}", LOG_FILE);
    $photos_number = ($fbUser->getLastDownloadedPhotos() > 0) ?
        $fbUser->getLastDownloadedPhotos() :
        $fbUser->getNumPhotos();

    utils::log("download_photos: search_last_photo_index: $index", LOG_FILE);
    utils::log("download_photos: photos_number: $photos_number", LOG_FILE);

    for($i = $index; $i < $num_photos_in_file && $downloaded_photos < $photos_number; $i++){
        $id_photo = str_replace(PHP_EOL, '', $file_content[$i]);
        utils::log("id_photo: $id_photo at: {$fbUser->getAccessToken()}", LOG_FILE);
        $photo = requestFBImage($fb, $id_photo, $fbUser->getAccessToken());
        if(isset($photo)) {
            $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$fbUser->getToken()}/$id_photo.jpg";
            utils::log("download_photos: img: $img", LOG_FILE);
            downloadFBPhoto($img, $photo);
        }
        $downloaded_photos++;
    }
    updatePhotoInfo($fbUser->getToken(), $downloaded_photos, $id_photo);
    
    //mostrarem les fotos al tester de facebook
    $filename = $_SERVER["DOCUMENT_ROOT"] . IMAGES_PATH . "/{$fbUser->getToken()}/{$fbUser->getToken()}.txt";
    $archivo = fopen($filename,'r');
    echo '<p style="color:white;">-- This is a Dummy PhotoBooth Screen simulator ;) --</p><br/>';
    echo '<button type="button">PRINT YOUR PHOTOS</button><br/>';
    while ($id_photo = fgets($archivo)) {
        $img =  IMAGES_PATH . "/{$fbUser->getToken()}/$id_photo.jpg";
        echo '<img src="https://myphotocode.com'.$img.'" alt="I love Facebook, is the best" width="50" height="60"><br/>';
        $aux[] = $img;    
        $numlinea++;
    }
    fclose($archivo);
    echo '<button type="button">PRINT YOUR PHOTOS</button>';
//    echo '<pre>';
//    print_r($aux);
//    echo '<pre>';
    
   //$img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$fbUser->getToken()}/$id_photo.jpg"; 
} catch(Exception $e){
    utils::log("download_photos: ". getInputToken(). " error: {$e->getMessage()}", LOG_FILE);
    echo $e->getMessage();
}

function updatePhotoInfo($token, $lastDownloadedPhotos, $lastPhoto){
    global $fbRepository;
    utils::log("updatePhotoInfo: img: $token- $lastDownloadedPhotos - $lastPhoto", LOG_FILE);
    return $fbRepository->updateDownloadedPhotosAndLastPhoto($token, $lastDownloadedPhotos, $lastPhoto);
}

function createFBClient(){
    return new \Facebook\Facebook([
        'app_id' => APP_ID,
        'app_secret' => APP_SECRET,
        'default_graph_version' => DEFAULT_GRAPH_VERSION
    ]);
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        if (empty(getInputToken())) {
            utils::log("Download error: Missing token", LOG_FILE);
            throw new Exception("{\"status\": \"KO\", \"message\":\"Missing token\"}");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

function getInputToken(){
    if (array_key_exists('token', $_GET) && !empty($_GET['token'])) {
        return $_GET['token'];
    }
    return "";
}

function getPhotoIds($fbUser) {
    $filename = $_SERVER["DOCUMENT_ROOT"] . IMAGES_PATH . "/{$fbUser->getToken()}/{$fbUser->getToken()}.txt";
    if (!file_exists($filename)) {
        throw new Exception("download_photos: missing $filename");
    }
    $file_content = @file($filename);
    return $file_content;
}

function getFBUser($token) {
    global $fbRepository;

    return $fbRepository->findByToken($token);
}

function requestFBImage($fb, $id_photo, $access_token){
    $photo = NULL;
    try {
        $response = $fb->get(
            "/$id_photo?fields=images",
            $access_token
        );
        $graphNode = json_decode($response->getGraphNode());
        $photo = $graphNode->images[0]->source;
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        utils::log( 'Graph returned an error: ' . $e->getMessage(), LOG_FILE);
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        utils::log( 'Facebook SDK returned an error: ' . $e->getMessage(), LOG_FILE);
        exit;
    }
    return $photo;
}

function searchLastPhotoIndex($file_content, $last_photo){
    $index = 0;
    if(isset($last_photo)){
        foreach($file_content as $line){
            $index++;
            if(str_replace(PHP_EOL, '', $line) == $last_photo){
                break;
            }
        }
    }
    return $index;
}

function downloadFBPhoto($final_image, $photo_url){
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

