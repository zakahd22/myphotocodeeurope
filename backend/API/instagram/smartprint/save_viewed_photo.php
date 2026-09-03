<?php
/****************************************************************************************************************
SAVE_VIEWED_PHOTO

Guarda vegades que una foto ha estat visualitzada per l’usuari a la taula InstagramPhotoViewed.

Si ja havia estat guardada s’incrementa un contador +1 (camp count de la BD). 
En cas que l’usuari acabi imprimint la foto s’incrementa +1 el contador de prints
Guardem pais segons la ip. Si no s’ha guardat per aquell país no fem update, creem un registre nou.
Guardem també nom de la carpeta a la que hi ha la foto.

Endpoint API/instagram/smartprint/save_viewed_photo.php

Input
prt, 1 = ha imprés, 0  = no ha imprés
view, 1 = ha visualitzat, 0  = no ha visualitzat
id photo, id de la foto visualitzada o impresa. És el propi nom de la foto sense extensió.
Output
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

/*************************************************************************************************************************
****************************************************************************************************************
//TODO: REVISA TOT EL QUE FA, ESTA PER CONSTRUIR D'AQUI CAP A SOTA
***********************************************************************************************************/
try {
    $igRepository = new PDOInstagramRepository();    
    utils::log("save_suggestion starts", LOG_FILE);
    guardAgainstMissingParameters();    

    $igUser = $igRepository->findByToken($_GET['tkn']);    
    if(empty($igUser->getAccessToken())){
        $result = "KO";
    } else {
   
        if(!isset($_GET['prt'])){
            $print=0;
        }else{
            $print=$_GET['prt'];
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $pais = getCountryByIp($ip);  

        $action = savePhotoViewed($_GET['idphoto'], $_GET['w'], $_GET['typ'], $pais, $print, $_GET['view']);
       
        
        $result = "OK";
    }
    
    utils::log("save_viewed_photo $result", LOG_FILE);
    echo $result;
} catch( Exception $e){     
    utils::log("save_viewed_photo: ". $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
}


function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        
        if (array_key_exists('w', $_GET) && !isset($_GET['w'])) {
            throw new Exception("Invalid word");
        }
        if (array_key_exists('id', $_GET) && !isset($_GET['id'])) {
            throw new Exception("Invalid id booth");
        }
        if (array_key_exists('idphoto', $_GET) && !isset($_GET['id'])) {
            throw new Exception("Invalid id photo");
        }
        if (array_key_exists('typ', $_GET) && !isset($_GET['typ'])) {
            throw new Exception("Invalid typ");
        }
        if (array_key_exists('prt', $_GET) && !isset($_GET['prt'])) {
            throw new Exception("Invalid prt");
        }
        if (array_key_exists('view', $_GET) && !isset($_GET['view'])) {
            throw new Exception("Invalid view");
        }

    } else {
        throw new Exception("Not a GET request");
    }
}

function savePhotoViewed($id_photo, $word, $type, $pais, $numPrint, $numCount){
    
    global $igRepository;

    return $igRepository->savePhotoViewed($id_photo, $word, $type, $pais, $numPrint, $numCount);
}

function getCountryByIp($ip) {
    global $igRepository;

    return $igRepository->getCountryByIp($ip);
}