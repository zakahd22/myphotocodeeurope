<?php

use facebook\smartprint\infrastructure\PDOFacebookRepository;

require_once 'config/fb_config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common/global.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once "infrastructure/PDOFacebookRepository.php";
utils::log('entra', 'logalex');
try {
    $fbRepository = new PDOFacebookRepository();
    guardAgainstMissingParameters();
    utils::log("end_session token: ". $_GET['tkn'] . ", id: ". $_GET['id'], LOG_FILE);
    deleteSession($_GET['tkn'], $_GET['id']);
    //20220128 Si necessitem crear un nou usuari de test per Facebook Meta ho comentarem temporalment perque si el token es el de proves de facebook no elimini mai la carpeta i fotos   
    system("rm -rf ". $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$_GET['tkn']}");
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
    global $fbRepository;

    $fbRepository->deleteByTokenAndIdBooth($token, $idPB);
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