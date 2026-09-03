<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use instagram\smartprint\infrastructure\PDOInstagramRepository;

require_once "infrastructure/PDOInstagramRepository.php";
require_once "config/ig_config.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/global.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/conexio.php';

// require_once 'config/ig_config.php';
require_once '../../../common/global.php';
require_once '../../../common/conexio.php';
// require_once __DIR__ . '/../../../vendor/autoload.php'; // change path as needed

try {   
    $igRepository = new PDOInstagramRepository();    
     
    guardAgainstMissingParameters();
   
    $word = getInputWord();
    $type =  getInputType(); 
    $n = getInputN();
    $downloaded_photos = 0;
    $file_content = getPhotoIds($word);
    //print_r($file_content);exit;
    $num_photos_in_file = count($file_content);
    
    if($num_photos_in_file>=$n && $type=='hashtag'){
       $nToDownload = $n; 
    }else{
       $nToDownload = $num_photos_in_file;
    }
    
    utils::log("download_photos: num_photos_in_file: $num_photos_in_file", LOG_FILE);
    utils::log("download_photos: n: $n", LOG_FILE);
    utils::log("download_photos: type: $type", LOG_FILE);

    //de moment =0, les tirem totes de cop
    $index=0;  
    $numhash = $_GET['numhash'];
    $ip = $_GET['ip'];
    
    
    // utils::log("download_photos: photos_number: $photos_number", LOG_FILE);

   
    //for($i = $index; $i < $num_photos_in_file; $i++){
    for($i = $index; $i < $nToDownload; $i++){
        $file_explode = explode("|",$file_content[$i]);
        
        $id_photo = str_replace(PHP_EOL, '', $file_explode[0]);
        
        utils::log("id_photo: $id_photo at: {$word}", LOG_FILE);
        $photo = str_replace(PHP_EOL, '', $file_explode[1]);
        $numLikes = $file_explode[2];
        $text = serialize($file_explode[2]);

       
       
        if(isset($photo)) {
            //Descarreguem la foto
            $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$word}/$id_photo.jpg";
            
            utils::log("download_photos: img: $img", LOG_FILE);
         // print "eeeeooo".$id_photo.$word.$type;
            downloadIGPhoto($img, $photo);






//Això, no. ho guardarem a get_photos. 
//TODO: Aquí la marquem com a DESCARREGADA
            if($i<=$numhash){
                $type = "hashtag";
            }else{
                $type = "username";
            } 
           
            $existsPhoto = getPhotoById($id_photo);
            
            if($existsPhoto){  
                     
                updateIGPhotoDownload($id_photo);
            }          
            //Guardem a BD la foto
            /*//TODO: Guarda vegades que una foto ha estat visualitzada per l’usuari a la taula InstagramPhotoViewed.

            1.-Si ja havia estat guardada s’incrementa un contador +1 (camp numCount de la BD).

            Guardem pais segons la ip. Si no s’ha guardat per aquell país no fem update, creem un registre nou.
            Guardem també nom (aqui $token) de la carpeta a la que hi ha la foto.
            Camps a guardar a InstagramPhotoViewed:
            -numCount: +1
            -word: $token
            -id: $id_photo
            -path: $img
            -type: hashtag o username. Ho sabem pel contador de hastags i username que rebem per parametre get.
            -data: data d'avui. el dia en que estem guardant la foto perque la hem descarregat de Instagram.
            */
           //  if($i<=$numhash){
           //      $type = "hashtag";
           //  }else{
           //      $type = "username";
           //  } 

           // $pais = getCountryByIp($ip);   

           // // $pais = 'us';         

           //  $existsPhoto = getPhotoByIdCountryType($id_photo, $pais, $type);
           //  // print $existsPhoto.$id_photo.$pais;

           //  if(!$existsPhoto){
 
           //      saveIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, $text);
                
           //   }else{
           //      updateIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, 0, 1);
           //  }
           



        }
        
    }
   
} catch(Exception $e){    
    utils::log("download_photos: ". getInputToken(). " error: {$e->getMessage()}", LOG_FILE);
    echo $e->getMessage();
}



function guardAgainstMissingParameters(){
    if(isset($_GET)) {
        if (empty(getInputToken())) {
            utils::log("Download error: Missing token", LOG_FILE);
            throw new Exception("{\"status\": \"KO\", \"message\":\"Missing token\"}");
        }
        if (empty(getInputWord())) {
            utils::log("Download error: Missing word", LOG_FILE);
            throw new Exception("{\"status\": \"KO\", \"message\":\"Missing word\"}");
        }
    } else {
        throw new Exception("Not a GET request");
    }
}

function getInputToken(){
    if (array_key_exists('tkn', $_GET) && !empty($_GET['tkn'])) {
        return $_GET['tkn'];
    }
    return "";
}

function getInputWord(){
    if (array_key_exists('w', $_GET) && !empty($_GET['w'])) {
        return $_GET['w'];
    }
    return "";
}

function getInputN(){
    if (array_key_exists('n', $_GET) && !empty($_GET['n'])) {
        return $_GET['n'];
    }
    return 100; //si no n'hi ha tantes baixarà les que tingui per defecte return 100; //si no n'hi ha tantes baixarà les que tingui per defecte

}

function getInputType(){
    if (array_key_exists('type', $_GET) && !empty($_GET['type'])) {
        return $_GET['type'];
    }
    return "";
}

function getPhotoIds($word) {
    $filename = $_SERVER["DOCUMENT_ROOT"] . IMAGES_PATH . "/{$word}/{$word}.txt";
    if (!file_exists($filename)) {
        throw new Exception("download_photos: missing $filename");
    }
    $file_content = @file($filename);
    return $file_content;
}


function downloadIGPhoto($final_image, $photo_url){
    $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_URL, $photo_url);
    $result=curl_exec($ch);
    file_put_contents($final_image, $result);
}

function saveIGPhotoDB($id_photo, $word, $path, $type, $pais, $numLikes, $text){
    //TODO: Aqui un select a InstagramPhotoViewed per id=idPhoto
    //1.- si exiteix incrementem +1 numcount
    //2.- si no existeix guarda id=$id_photo, numPrint=0, numCount=1, word=$word, type=$type, path=$path, downloadDate=, numlikes

    //3.-com controlarem quines enviem a cada petició segons el numero que ens en demanin??
    global $igRepository;

    return $igRepository->insertPhotoViewed($id_photo, $word, $path, $type, $pais, $numLikes, $text);
}

function updateIGPhotoDownload($id_photo){
    
    global $igRepository;

    return $igRepository->updateIGPhotoDownload($id_photo);
}


function getPhotoByIdCountryType($id_photo, $pais, $type) {
    global $igRepository;

    return $igRepository->getPhotoByIdCountryType($id_photo, $pais, $type);
}

function getPhotoById($id_photo) {
    global $igRepository;

    return $igRepository->getPhotoById($id_photo);
}


function getCountryByIp($ip) {
    global $igRepository;

    return $igRepository->getCountryByIp($ip);
}



