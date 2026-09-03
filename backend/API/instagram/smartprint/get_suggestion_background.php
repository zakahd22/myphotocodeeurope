<?php
/****************************************************************************************************************************************
Obté tots els sugeriments dels hashtag/username que hi ha a la BD de MyPC a la taula InstagramSuggestions

Endpoint API/instagram/smartprint/get_suggestion_background.php


Input
(només els comuns)

Output
(valors comuns)
numero de items del type username
numero de items del type hashtag
llista de usernames separats per ‘|’. Per exemple: rosalia|rosaliaoficial|rosaliafans|larosalia 
Entre pipelines hi concatenaré separat per + la photoPerfil, isVerified i numfollowers. Ordenades per numFollowers, numPrint i numCount.
Per exemple: |rosalia.vt+url_de_la_photo+1+345333|
llista de hashtags separats per ‘|’. Per exemple: rosalia|rosaliasongs|rosaliamtv
Exemple: #numitems#paraula 1|paraula 2| //ordenades per numCount i print DESC. 

******************************************************************************************************************************************/
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

***********************************************************************************************************/

try {
    $igRepository = new PDOInstagramRepository();
    
    utils::log("get_suggestion_background starts", LOG_FILE);

      
    
        guardAgainstMissingParameters(); //no cal, si no hi ha paraula retornem tots els suggeriments


        if(!isset($_GET['typ'])){
           
         $type = ''; 
        }else{
         $type = $_GET['typ']; 
        }

        if(!isset($_GET['n']) || $_GET['n']==''){
           
         $limit = 100000;
        }else{
         $limit = $_GET['n']; 
        }
          
        $findAllSuggestion = findAllSuggestion(  
            $type,    
            $limit
        );
        
        $suggestionsByType = concatSuggestionsByType($findAllSuggestion, $type);
        $result = "OK#$suggestionsByType";
   
    utils::log("get_suggestion_background $result", LOG_FILE);
    echo $result;
} catch( Exception $e){
     
    utils::log("get_suggestion_background error: ". $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
}

//funcio que recorre l'array getSuggestion, conta quants hashtags i username hi ha i concatena cadena amb el següent format |numhash|numuser|paraula 1|paraula 2| 
//ordenades per count, valor que se'ls dona quan algu ho busca o imprimeix. 
function concatSuggestionsByType($suggestions,$type=''){
    $token = "BCOMTD"; //el passem fixe no mirem pas si es correcte de moment. TODO: podriem comprovar que es un token de la BD...
    $countHashtags=0;
    $countUsernames=0;
    $concatUsernames = '';
    $concatHashtags = '';
   
    foreach ($suggestions as $suggestion) {
        
        if($suggestion['type']=='hashtag'){
            $countHashtags++;
            $concatHashtags .= $suggestion['word']."|";

        }
        if($suggestion['type']=='username'){
            $countUsernames++;
            if($suggestion['fbid']){
                $fbidImg =  PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto={$suggestion['fbid']}&w={$suggestion['fbid']}&ext=jpg&tkn={$token}";
                $concatUsernames .= $suggestion['word']."+".$fbidImg."+".$suggestion['isVerified']."+".$suggestion['numFollowers']."|";
                
            }else{
                $concatUsernames .= $suggestion['word']."|";
            }
            
        }
        $i++;

    }

    

    
        $concatsuggestions = $countUsernames."#".$countHashtags."#".$concatUsernames."#".$concatHashtags;
    
        
   
   

    $concatsuggestions = substr($concatsuggestions, 0, -1); //eliminem l'ultim |
    return $concatsuggestions;
}

function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        
       
        if (!array_key_exists('id', $_GET) || !isset($_GET['id']) || $_GET['id'] == "") {
            throw new Exception("Missing PB id");
        }
        if (!array_key_exists('code', $_GET) || !isset($_GET['code']) || $_GET['code'] == "") {
            throw new Exception("Missing game code");
        }

    } else {
        throw new Exception("Not a GET request");
    }
}



function findAllSuggestion($type, $limit) {
    global $igRepository;

    return $igRepository->findAllSuggestion($type, $limit);
}