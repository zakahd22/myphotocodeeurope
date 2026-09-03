<?php
/****************************************************************************************************************
REPORT_PHOTO
Permet guardar a la taula reportedPhotos quan un usuari clica el botó per a reportar-ne una.

Endpoint API/instagram/smartprint/photo.php

Input
id, identificador de la foto
mt, motiu, camp text (Estàn per establir valors possibles. Per exemple:  No m’agrada, en sóc el propietari)
(valors comuns)

*****************************************************************************************************************/
error_reporting(0);
ini_set('display_errors', 1);

use instagram\smartprint\infrastructure\PDOInstagramRepository;


require_once "infrastructure/PDOInstagramRepository.php";
require_once "config/ig_config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/global.php';
require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/conexio.php';

// require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'; // change path as needed


try {
    $igRepository = new PDOInstagramRepository();    
    utils::log("report_photo starts", LOG_FILE);
    guardAgainstMissingParameters();
    
    $igUser = $igRepository->findByToken($_GET['tkn']);    
    if(empty($igUser->getAccessToken())){
        echo "KO";
    } else {
        $action = insertReport( 
            $_GET['idphoto'],
            $_GET['mt']
        );
        $result = "OK";
        
    }


   
    
    
    utils::log("report_photo $result", LOG_FILE);
    echo $result;
} catch( Exception $e){     
    utils::log("get_photos error: ". $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
}


function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        
        if (array_key_exists('idphoto', $_GET) && !isset($_GET['idphoto'])) {
            throw new Exception("Invalid id photo");
        }
        if (array_key_exists('mt', $_GET) && !isset($_GET['mt'])) {
            throw new Exception("Invalid reason mt");
        }   

    } else {
        throw new Exception("Not a GET request");
    }
}


function insertReport($id, $mt) {
    global $igRepository;

    return $igRepository->insertReport($id, $mt);
}

