<?php

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use facebook\smartprint\infrastructure\PDOFacebookRepository;

require_once 'config/fb_config.php';
require_once '../../../common/global.php';
require_once "infrastructure/PDOFacebookRepository.php";
require_once "domain/FacebookUser.php";

//session_start();
require_once __DIR__ . '/../../../vendor/autoload.php'; // change path as needed

try {
    $fbRepository = new PDOFacebookRepository();
    $input = getJsonData();
    validatePostInput($input);
    $token = strtoupper($input['token']);
    $name = $input['name'];
    $access_token = $input['access_token'];

    checkAndUpdateToken($token, $access_token, $name);
    getUserPhotos(createFBClient(), $access_token, $token);
    echo "{\"status\": \"OK\"}";
} catch(Exception $e){
    http_response_code(404);
    utils::log("end_session " . getInputToken() . " error: ". $e->getMessage(), LOG_FILE);
    echo "{\"status\": \"KO\", \"message\":\"{$e->getMessage()}\"}";
}

function getInputToken(){
    return array_key_exists('token', $_GET) && !empty($_GET['token']) ?
        $_GET['token']
        :
        "";
}

function checkAndUpdateToken($token, $access_token, $name){
    global $fbRepository;

    $fbUser = $fbRepository->findByToken($token);

    
  
    
    if(empty($fbUser->getToken())){
        throw new Exception("Token not founds");
    }

    if(!empty($fbUser->getAccessToken()) && $token!="QILHZF"){
        throw new Exception("Access token already set");
    }
      //20220128 deixem un usuari perque facebook sempre que vulgui pugui fer test de l'app i no ens la desactivi
    
      if($token=="QILHZF"){
          
            $fbRepository->updateAccessTokenAndName($token, $access_token, $name);
            $fbRepository->updateAllPhotosServed($fbUser->getToken(), 0);
            getUserPhotos(createFBClient(), $access_token, $token);
//            window.open("https://myphotocode.com/API/facebook/smartprint/get_photos_test.php?id=8173&tkn=".$token."&n=6&code=WKDK7YE001", '_blank');
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Allow: GET, POST, OPTIONS, PUT, DELETE");
            $method = $_SERVER['REQUEST_METHOD'];
            if($method == "OPTIONS") {
                die();
            }

            header("Location: ".$_SERVER['HTTP_REQUEST']."/API/facebook/smartprint/get_photos_test.php?id=8332&tkn=".$token."&n=6&code=W7UX3SCJ6D"); /* Redirección del navegador */
            /* Asegurándonos de que el código interior no será ejecutado cuando se realiza la redirección. */
            exit;
//            throw new Exception("redireccionate por 0");
      }

    $fbRepository->updateAccessTokenAndName($token, $access_token, $name);
}


function validatePostInput($input){
    if(!array_key_exists('token', $input) || !isset($input['token'])){
        throw new Exception("Missing token");
    }
    if(!array_key_exists('name', $input) || !isset($input['name'])){
        throw new Exception("Missing user name");
    }
    if(!array_key_exists('access_token', $input) || !isset($input['access_token'])){
        throw new Exception("Missing access_token");
    }
}

function getJsonData() {
    $inputJSON = file_get_contents('php://input',TRUE);
    if(empty($inputJSON)){
        throw new Exception("No POST data");
    }
    return json_decode($inputJSON, TRUE);
}

function getUserPhotos($fb, $access_token, $token){
    $response = getAlbumsPhotos($fb, $access_token);
    $decodedBody = $response->getDecodedBody();
    $fp = createImagesDirectory($token);

    foreach($decodedBody['albums']['data'] as $album){
        foreach($album['photos']['data'] as $photo){
            fputs($fp, "{$photo['id']}\n");
        }
    }
    fclose($fp);
    updateUserTotalPhotos($token);
    downloadNextPhotosInBackground($token);
}

function updateUserTotalPhotos($token){
    global $fbRepository;
    $file = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt";
    $totalLines = intval(exec("cat '$file'|wc -l"));
    if($totalLines>0) {
        $fbRepository->updateTotalPhotos($token, $totalLines);
    }
}

function getAlbumsPhotos($fb, $access_token){
    if(empty($access_token)) {
        throw new Exception("Could not get an access token");
    }
    try {
        return $fb->get(
            '/me?fields=albums.fields(photos.fields(image))',
            $access_token
        );
    } catch (Facebook\Exceptions\FacebookResponseException  $e) {
        throw new Exception($e->getMessage());
    } catch( Facebook\Exceptions\FacebookSDKException $e){
        throw new Exception($e->getMessage());
    }
}

function createFBClient(){
    return new \Facebook\Facebook([
        'app_id' => APP_ID,
        'app_secret' => APP_SECRET,
        'default_graph_version' => DEFAULT_GRAPH_VERSION
    ]);
}

function createImagesDirectory($token){
    $fp = NULL;
    if(isset($token)){
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token");
        }
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "w");
    }
    return $fp;
}

function downloadNextPhotosInBackground($token){
    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/facebook/smartprint/download_photos.php?token=$token\" >> ".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat 2>&1 &";
    utils::log("downloadNextPhotos $command", "numa");
    exec($command);
}
