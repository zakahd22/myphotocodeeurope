<?php
/****************************************************************************************************************************************
GET_SUGGESTION
Obté sugeriments dels hashtag/username buscats similars a la paraula introduïda per l’usuari (*rosalia* → rosalia.vt, larosalia)
Aquests hashtag són emmagatzemats en BD sempre que la crida doni resultat mitjançant la crida save_suggestion.
Si ja havia estat guardada s’incrementa un contador +1 (camp count de BD). 
En cas que l’usuari acabi imprimint pel hashatag s’incrementa +1 el camp print de la BD.
La mateixa crida sense paraula (paràmetre w) al get pot retornar tots els hashtag i username de la BD perquè el photobooth els pugui descarregar una sola vegada al dia, per exemple.
Segons la IP desde la que es fa la crida li retornarem només suggestions del propi país. (quan decidim filtrar-ho, de moment ho tornem tot.)


Endpoint API/instagram/smartprint/get_suggestion.php

Input
w, paraula a cercar. Torna totes les coincidencies que contenen la paraula. Si no s’especifica retorna totes les paraules de la base de dades
typ, si volem filtrar per tipus (typ=hashtag o typ=username). Si no s’especifica els torna tots.
Output
numero de hastags
numero de usernames
llista de usernames separats per ‘|’. Per exemple: rosalia|rosaliaoficial|rosaliafans|larosalia
llista de hashtags separats per ‘|’. Per exemple: rosalia|rosaliasongs|rosaliamtv
Exemple: |numhashtags|numusernames|paraula 1|paraula 2|     //ordenades per count i print DESC. En el cas dels usuaris ordenades per numFollowers, prints i count.
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
//TODO: modificacions v2: 
-----ordenades per: numfollowers(nomes usuari), numPrint, numCount
-----filtrades per: pais (si passen valor)
//TODO: modificacions v3: 
-----Retorn de Other ideas si hashtag i username a suggerir =0
----------Retornem 5 OtherHashtags i 5 OtherUsernames
***********************************************************************************************************/

try {
    $igRepository = new PDOInstagramRepository();
    
    utils::log("get_suggestion starts", LOG_FILE);

    $igUser = $igRepository->findByToken($_GET['tkn']);    
    if(empty($igUser->getAccessToken())){
        echo "KO";
    } else {
        guardAgainstMissingParameters(); //no cal, si no hi ha paraula retornem tots els suggeriments


        if(!isset($_GET['w'])){
           
         $word = ''; 
        }else{
         $word = $_GET['w']; 
        }
        if(!isset($_GET['typ'])){
           
         $type = 'hashtag'; 
        }else{
         $type = $_GET['typ']; 
        }

        if(!isset($_GET['n'])){
           
         $limit = 0; 
        }else{
         $limit = $_GET['n']; 
        }

        if(!isset($_GET['other'])){
           
         $other = 0; 
        }else{
         $other = $_GET['other']; 
        }
        
       
        $findLikeWord = findLikeWord(
            $word, 
            $type,
            $limit
        );
        $findOtherIdeas = array();
        //Aquest condicional era per retornar tot junt otherideas quan n'hi havia menys que n
//        if(count($findLikeWord)<1){ //podem fer que suggereixi si es menor a un altre valor
            $findOtherIdeas = findOtherIdeas(  
                $type,    
                $limit
            );
//        }
        
       
        //Funcio que recorre l'array getSuggestion, conta quants hashtags i username hi ha i concatena cadena amb el següent format |numhash|numuser|paraula 1|paraula 2| 
        //ordenades per count, valor que se'ls dona o incrementa quan algu ho busca o imprimeix. 
        $suggestionsByType = concatSuggestionsByType($findLikeWord, $findOtherIdeas, $type, $other);

        $result = "OK#$suggestionsByType";
    }
    utils::log("get_suggestion $result", LOG_FILE);
    echo $result;
} catch( Exception $e){
     
    utils::log("get_suggestion error: ". $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
}

//funcio que recorre l'array getSuggestion, conta quants hashtags i username hi ha i concatena cadena amb el següent format |numhash|numuser|paraula 1|paraula 2| 
//ordenades per count, valor que se'ls dona quan algu ho busca o imprimeix. 
function concatSuggestionsByType($suggestions,$suggestionsOther, $type='', $other=0){
    $token = "BCOMTD"; //el passem fixe no mirem pas si es correcte de moment. TODO: podriem comprovar que es un token de la BD...
    $countHashtags=0;
    $countUsernames=0;
    $concatUsernames = '';
    $concatHashtags = '';
    //Other ideas
    $countHashtagsOther=0;
    $countUsernamesOther=0;
    $concatUsernamesOther = '';
    $concatHashtagsOther = '';
    
if($other==1){
    foreach ($suggestionsOther as $suggestion) {
        
        if($suggestion['type']=='hashtag'){
            $countHashtagsOther++;
            $concatHashtagsOther .= $suggestion['word']."|";

        }
        if($suggestion['type']=='username'){
            $countUsernamesOther++;
            if($suggestion['fbid']){
                $fbidImg =  PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto={$suggestion['fbid']}&w={$suggestion['fbid']}&ext=jpg&tkn={$token}";
                $concatUsernamesOther .= $suggestion['word']."+".$fbidImg."+".$suggestion['isVerified']."+".$suggestion['numFollowers']."|";
                
            }else{
                $concatUsernamesOther .= $suggestion['word']."|";
            }
            

            
        }
        $i++;

    }
    
}else{
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
    
}
    
    //vam definit inicialment la crida per a retornar hashtag, usernames i other ideas tot junt
    // $concatsuggestions = $countHashtags."#".$countUsernames."#".$countHashtagsOther."#".$countUsernamesOther."#".$concatHashtags.$concatUsernames.$concatHashtagsOther.$concatUsernamesOther;

    //retornem només el type que hem definit

    if($type=='hashtag' && $other==0){
        $concatsuggestions = $countHashtags."#".$concatHashtags;
    }
    elseif($type=='username' && $other==0){
        $concatsuggestions = $countUsernames."#".$concatUsernames;
    }    
    elseif($type=='hashtag' && $other==1){
        $concatsuggestions = $countHashtagsOther."#".$concatHashtagsOther;
    }    
    elseif($type=='username' && $other==1){

    $concatsuggestions = $countUsernamesOther."#".$concatUsernamesOther;
    }

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
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("Missing token");
        }
        if (array_key_exists('w', $_GET) && !isset($_GET['w'])) {
            throw new Exception("Invalid word");
        }
        if (array_key_exists('n', $_GET) && !isset($_GET['n'])) {
            throw new Exception("Invalid number of photos");
        }

    } else {
        throw new Exception("Not a GET request");
    }
}

function findLikeWord($word, $type, $limit) {
    global $igRepository;

    return $igRepository->findLikeWord($word, $type, $limit);
}

function findOtherIdeas($type, $limit) {
    global $igRepository;

    return $igRepository->findOtherIdeas($type, $limit);
}