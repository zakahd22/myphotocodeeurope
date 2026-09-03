<?php

use instagram\smartprint\infrastructure\PDOInstagramRepository;

require_once 'config/ig_config.php';

require_once $_SERVER['DOCUMENT_ROOT']  . LOCAL_PATH .  '/common/global.php';
//require_once $_SERVER['DOCUMENT_ROOT']  . LOCAL_PATH . '/vendor/autoload.php';
require_once "infrastructure/PDOInstagramRepository.php";
utils::log('entra', 'logalex');

try {
    $igRepository = new PDOInstagramRepository();
    guardAgainstMissingParameters();
    utils::log("end_session token: ". $_GET['tkn'] . ", id: ". $_GET['id'], LOG_FILE);
    deleteSession($_GET['tkn'], $_GET['id']);
    //system("rm -rf ". $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$_GET['tkn']}");
    echo "OK#OK";
} catch (Exception $e) {
    utils::log("end_session ". getInputToken() ." error: " . $e->getMessage(), LOG_FILE);
    echo $e->getMessage();
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
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("Missing token");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

function deleteSession($token, $idPB) {
    global $igRepository;

    $igRepository->deleteByTokenAndIdBooth($token, $idPB);
}

function validateParams(){
    if(isset($_GET)) {
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("OK#KO#Missing token");
        }
        if (!array_key_exists('id', $_GET) || !isset($_GET['id'])) {
            throw new Exception("OK#KO#Missing PB id");
        }
    }
}