<?php
/****************************************************************************************************************
SAVE_SUGGESTION
Guarda suggeriments dels hashtag/username buscats per l’ususari a la taula InstagramSuggestions.
Aquests hashtag són emmagatzemats en BD sempre que la crida doni resultat mitjançant la crida save_suggestion.
Si ja havia estat guardada s’incrementa un contador +1 (camp count de la BD). 
En cas que l’usuari acabi imprimint pel hashatag s’incrementa +1 el contador de prints
Guardarem també el numero de followers. (de moment no veig necessitat que es passi per paràmetre, la crida tornarà a comprovar el numero consultant l’usuari a Instagram.)
Guarda el país segons la IP desde el que es fa la crida per a poder retornar suggestions del propi país.



Endpoint API/instagram/smartprint/save_suggestion.php

Input
prt, 1 = ha imprés, 0  = no ha imprés
w, paraula cercada o hastag/username seleccionat
typ, tipus de cerca. Valors possibles: hashtag, username
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

/******************************************************************************************
-----guarda per separat: 
        -numFollowers: (fet)
        -numPrint (fet)
        -numCount (fet)
        -pais     (fet)
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

            $pais = getCountryByIp($_SERVER['REMOTE_ADDR']);  

            $countMatches = getWord(
                $_GET['w'],        
                $_GET['typ'],
                $pais
            );
         
            if($countMatches){    
            
                $action = updateSuggestionSenseFollowers( 
                    $_GET['w'],
                    $_GET['prt'],
                    $_GET['typ'],
                    $pais
                );
            }
            else{  
                
                $action = insertSuggestionSenseFollowers( 
                    $_GET['w'],
                    $_GET['prt'],
                    $_GET['typ'],
                    $pais,
                        0,
                        0
                );

            }





    
    $result = "OK";
       
        
    }

    
   
    
    
    
    utils::log("save_suggestion $result", LOG_FILE);
    echo $result;
} catch( Exception $e){     
    utils::log("save_suggestion error: ". $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
}


function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        
        if (array_key_exists('w', $_GET) && !isset($_GET['w'])) {
            throw new Exception("Invalid word");
        }

    } else {
        throw new Exception("Not a GET request");
    }
}

function getWord($word, $type, $pais) {
    global $igRepository;

    return $igRepository->findByWord($word, $type, $pais);
}

function getCountryByIp($ip) {
    global $igRepository;

    return $igRepository->getCountryByIp($ip);
}



function updateSuggestionSenseFollowers($word, $print, $type, $pais) {
    global $igRepository;

    return $igRepository->updateSuggestionSenseFollowers($word, $print, $type, $pais);
}


function insertSuggestionSenseFollowers($word, $print, $type, $pais, $isVerified, $fbid) {
    global $igRepository;

    return $igRepository->insertSuggestionSenseFollowers($word, $print, $type, $pais, $isVerified, $fbid);
}