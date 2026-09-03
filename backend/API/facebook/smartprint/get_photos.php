<?php

error_reporting(0);
ini_set('display_errors', 1);


use facebook\smartprint\domain\FacebookUser;
use facebook\smartprint\infrastructure\PDOFacebookRepository;

require_once "infrastructure/PDOFacebookRepository.php";
require_once "domain/FacebookUser.php";

require_once "config/fb_config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/common/global.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common/conexio.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'; // change path as needed

try {
    $fbRepository = new PDOFacebookRepository();
    utils::log("get_photos starts", LOG_FILE);
    guardAgainstMissingParameters();
    $token = $_GET['tkn'];
    $fbUser = getFBUser(
        $token,
        $_GET['id']
    );

    $filename = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt";
    if($fbUser->getAllPhotosServed() == 1){
        echo "OK#OK#LOGIN#0";
        exit;
    }

    $urls = getNextPhotos($fbUser, $filename);

    if(isset($urls) && $urls != ""){
        downloadNextPhotosInBackground($fbUser);
        $result = "OK#OK#LOGIN#$urls";
    } else {
        $result = "OK#OK#LOGIN#0";
    }
    utils::log("get_photos $result", LOG_FILE);
    echo $result;
} catch( Exception $e){
    utils::log("get_photos " . getInputToken() . " error: ". $e->getMessage(), LOG_FILE);
    echo "OK#KO#{$e->getMessage()}";
}

function getInputToken(){
    return array_key_exists('tkn', $_GET) && !empty($_GET['tkn']) ?
        $_GET['tkn']
        :
        "";
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        if (!array_key_exists('id', $_GET) || !isset($_GET['id']) || $_GET['id'] == "") {
            throw new Exception("Missing PB id");
        }
        if (!array_key_exists('code', $_GET) || !isset($_GET['code']) || $_GET['code'] == "") {
            throw new Exception("Missing game code");
        }
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("Missing token");
        }
        if (array_key_exists('n', $_GET) && !isset($_GET['n'])) {
            throw new Exception("Invalid number of photos");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

function getFBUser($token, $pbId) {
    global $fbRepository;

    return $fbRepository->findByTokenAndIdBooth($token, $pbId);
}

function updateLastPhoto($token, $last_photo){
    global $fbRepository;

    $fbRepository->updateLastPhoto($token, $last_photo);
}

function getNextPhotos(FacebookUser $fbUser, $filename) {
    $urls = "";
    $photos_number = ($fbUser->getLastDownloadedPhotos() > 0) ?
        $fbUser->getLastDownloadedPhotos() :
        $fbUser->getNumPhotos();
    utils::log("getNextPhotos: photos_number: $photos_number", "numa");
    if($photos_number > 0 && isset($filename) && !empty($fbUser->getLastPhoto()) && !empty($fbUser->getToken())) {
        $fileContent  = @file($filename);
        $index = getNextPhotoIndex( $fbUser, $fileContent, $photos_number);
        utils::log("getNextPhotos: index: $index", "numa");
        if($index >= 0) {
            $j = 0;
            for ($i = $index; $j < $photos_number && $i < count($fileContent); $i++) {
                if ($j > 0) {
                    $urls .= "|";
                }
                $idPhoto = str_replace(PHP_EOL, '', $fileContent[$i]);
                $urls .= PHOTO_DOMAIN . "/API/facebook/smartprint/photo.php?id=$idPhoto&tkn={$fbUser->getToken()}&ext=jpg|jpg";
                $j++;
            }
        }
    }
    if($urls != "") {
        $urls = "$photos_number#$urls";
    }
    return $urls;
}

function getNextPhotoIndex(FacebookUser $fbUser, $fileContent, $photos_number){
    global $fbRepository;

    $index = 0;
    $num_photos_file = count($fileContent);
    if(is_array($fileContent) && isset($fileContent)){
        foreach($fileContent as $line){
            if(str_replace(PHP_EOL, '', $line) == $fbUser->getLastPhoto()){
                break;
            }
            $index++;
        }
    }
    if($index==$num_photos_file-1){
        $fbRepository->updateAllPhotosServed($fbUser->getToken(), 1);
    }

    return $index - ($photos_number-1);
}

function downloadNextPhotosInBackground(FacebookUser $fbUser){
    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/facebook/smartprint/download_photos.php?token={$fbUser->getToken()}\" >>".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat  2>&1 &";
    utils::log("downloadNextPhotos $command", "numa");
    exec($command);
}
