<?php

use facebook\smartprint\domain\FacebookUser;
use facebook\smartprint\domain\AppBoothDongle;
use facebook\smartprint\infrastructure\FBUserDTO;
use facebook\smartprint\domain\FacebookUserBuilder;
use facebook\smartprint\infrastructure\PDOFacebookRepository;
use facebook\smartprint\infrastructure\PDOAppBoothDongleRepository;

require_once "config/fb_config.php";
require_once "infrastructure/PDOFacebookRepository.php";
require_once "infrastructure/PDOAppBoothDongleRepository.php";
require_once "domain/FacebookUserBuilder.php";
require_once "domain/FacebookUser.php";
require_once "domain/AppBoothDongle.php";
require_once $_SERVER['DOCUMENT_ROOT']  . '/common/global.php';
require_once $_SERVER['DOCUMENT_ROOT']  . '/common/conexio.php';

try {
    $fbRepository = new PDOFacebookRepository();
    $dongleRepository = new PDOAppBoothDongleRepository();

    utils::log( "createFacebookUser: " . print_r($_GET, 1), LOG_FILE);

    guardAgainstMissingParameters();
    list($idBooth, $gameCode, $dongle) = sanitizeParameters();
    //validateDongle($idBooth, $dongle);

    $fbUser = createFacebookUser(
        $idBooth,
        tryGetNumberPhotos(),
        $gameCode
    );
    $fbRepository->persist($fbUser);
    $result = "OK#OK#{$fbUser->getToken()}#" . FB_LOGIN_URI . "#{$fbUser->getToken()}#" . FB_LOGIN_URI . "?tk={$fbUser->getToken()}";
    utils::log("init_session $result", LOG_FILE);
    echo $result;
} catch (Exception $e) {
    utils::log("init_session " . getInputBoothId() . " " . getInputCode()."error: " . $e->getMessage(), LOG_FILE);
	utils::log("OK#KO#{$e->getMessage()}", LOG_FILE);

    echo "OK#KO#{$e->getMessage()}";
}


function getInputCode(){
    return array_key_exists('code', $_GET) && !empty($_GET['code']) ?
        $_GET['code']
        :
        "";
}

function getInputBoothId(){
    return array_key_exists('id', $_GET) && !empty($_GET['id']) ?
        $_GET['id']
        :
        "";
}

function validateDongle($idBooth, $dongle){
    global $dongleRepository;
    $appDongle = $dongleRepository->findByIdBooth($idBooth);
    utils::log( "validateDongle: {$appDongle->getDongle()} - {$appDongle->getIdBooth()} $dongle", LOG_FILE);
    if($appDongle->getDongle() != $dongle){
	utils::log(" validateDongle excepion", LOG_FILE);
        throw new Exception("Invalid dongle");
    }
}

function sanitizeParameters(){
    //utils::log( "createFacebookUser: {$_GET['id']}-{$_GET['code']}-{$_GET['dongle']}" , LOG_FILE);
    if(!($idPB = filter_var($_GET["id"], FILTER_VALIDATE_INT))){
        throw new Exception("Wrong PB id format");
    }
    if(!($gameCode = filter_var($_GET["code"], FILTER_SANITIZE_STRING))){
        throw new Exception("Wrong game code format");
    }
    if(!($dongle = filter_var($_GET["dongle"], FILTER_VALIDATE_INT))){
        throw new Exception("Wrong dongle format");
    }

    return array($idPB, $gameCode, $dongle);
}

function tryGetNumberPhotos() {
    if (array_key_exists('n', $_GET) && isset($_GET['n']) && is_numeric($_GET['n']) && $_GET['n'] > 0) {
        return filter_var($_GET["n"], FILTER_VALIDATE_INT);
    }
    return DEFAULT_NUM_PHOTOS;
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        utils::log( "createFacebookUser: {$_GET['id']}-{$_GET['code']}-{$_GET['dongle']}" , LOG_FILE);
        if (!array_key_exists('id', $_GET) || empty($_GET['id'])) {
            throw new Exception("Missing PB id");
        }
        if (!array_key_exists('dongle', $_GET) || empty($_GET['dongle'])) {
            throw new Exception("Missing dongle");
        }
        if (!array_key_exists('code', $_GET) || empty($_GET['code'])) {
            throw new Exception("Missing game code");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

function createToken() {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), -TOKEN_LENGTH);
}

function createFacebookUser($idPB, $numPhotos, $gameCode) {
    return (new FacebookUserBuilder())
        ->withIdBooth($idPB)
        ->withNumPhotos($numPhotos)
        ->withGameCode($gameCode)
        ->withToken(createNewToken())
        ->build();
}

function createNewToken(){
    global $fbRepository;
    $token = NULL;
    $counter = 0;
    do{
        $counter++;
        $token = createToken();
        try {
            $fbRepository->findByToken($token);
        } catch(Exception $exception){
            if(!$exception->getMessage() == "Token not found") {
                throw new Exception("Unable to create new token");
            }
        }
    } while(!$token && $counter < MAX_TOKEN_RETRY);

    if(!$token) {
        throw new Exception("Unable to create new token");
    }
    return $token;
}
