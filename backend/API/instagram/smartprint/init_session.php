<?php

use instagram\smartprint\domain\InstagramUser;
use instagram\smartprint\domain\AppBoothDongle;
use instagram\smartprint\infrastructure\IGUserDTO;
use instagram\smartprint\domain\InstagramUserBuilder;
use instagram\smartprint\infrastructure\PDOInstagramRepository;
use instagram\smartprint\infrastructure\PDOAppBoothDongleRepository;

require_once "config/ig_config.php";
require_once "infrastructure/PDOInstagramRepository.php";
require_once "infrastructure/PDOAppBoothDongleRepository.php";
require_once "domain/InstagramUserBuilder.php";
require_once "domain/InstagramUser.php";
require_once "domain/AppBoothDongle.php";
// require_once $_SERVER['DOCUMENT_ROOT']  . '/common/global.php'; //no cal canviar per maquina local
// require_once $_SERVER['DOCUMENT_ROOT']  . '/common/conexio.php'; //no cal canviar per maquina local
require_once '../../../common/global.php'; //funciona en local
require_once '../../../common/conexio.php'; //funciona en local

try {
    $igRepository = new PDOInstagramRepository();
    $dongleRepository = new PDOAppBoothDongleRepository();

    utils::log( "createInstagramUser: " . print_r($_GET, 1), LOG_FILE);

    guardAgainstMissingParameters();
    list($idBooth, $gameCode, $dongle) = sanitizeParameters();
    //validateDongle($idBooth, $dongle);

    $igUser = createInstagramUser(
        $idBooth,
        tryGetNumberPhotos(),
        $gameCode
    );
    $igRepository->persist($igUser);
    
    $result = "OK#{$igUser->getToken()}#";
    utils::log("init_session $result", LOG_FILE);
    echo $result;
} catch (Exception $e) {
    utils::log("init_session " . getInputBoothId() . " " . getInputCode()."error: " . $e->getMessage(), LOG_FILE);
	utils::log("KO#{$e->getMessage()}", LOG_FILE);

    echo "KO#{$e->getMessage()}";
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
        utils::log( "createInstagramUser: {$_GET['id']}-{$_GET['code']}-{$_GET['dongle']}" , LOG_FILE);
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

function createInstagramUser($idPB, $numPhotos, $gameCode) {
    return (new InstagramUserBuilder())
        ->withIdBooth($idPB)
        ->withNumPhotos($numPhotos)
        ->withGameCode($gameCode)
        ->withToken(createNewToken())
        ->build();
}

function createNewToken(){
    global $igRepository;
    $token = NULL;
    $counter = 0;
    do{
        $counter++;
        $token = createToken();
        try {
            $igRepository->findByToken($token);
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
