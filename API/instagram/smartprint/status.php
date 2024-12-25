<?php

use instagram\smartprint\infrastructure\PDOInstagramRepository;
require_once 'config/ig_config.php';

//require_once '../../../common/global.php';
require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/global.php';
require_once "infrastructure/PDOInstagramRepository.php";

try {
    guardAgainstMissingParameters();
    $igRepository = new PDOInstagramRepository();
    $igUser = $igRepository->findByToken($_GET['tkn']);    
    if(empty($igUser->getAccessToken())){
        echo "KO";
    } else {
        echo "OK" , !empty($igUser->getTotalPhotos()) ? "#{$igUser->getTotalPhotos()}" : "";
    }
} catch (Exception $e) {
    utils::log("status " . getInputToken() . " error: " . $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        utils::log( "status: {$_GET['id']}-{$_GET['code']}-{$_GET['dongle']}" , LOG_FILE);
        if (!array_key_exists('id', $_GET) || empty($_GET['id'])) {
            throw new Exception("Missing PB id");
        }
        if (!array_key_exists('dongle', $_GET) || empty($_GET['dongle'])) {
            throw new Exception("Missing dongle");
        }
        if (!array_key_exists('code', $_GET) || empty($_GET['code'])) {
            throw new Exception("Missing game code");
        }
        if (!array_key_exists('tkn', $_GET) || empty($_GET['tkn'])) {
            throw new Exception("Missing token");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

function getInputToken(){
    return array_key_exists('tkn', $_GET) && !empty($_GET['tkn']) ?
        $_GET['tkn']
        :
        "";
}
