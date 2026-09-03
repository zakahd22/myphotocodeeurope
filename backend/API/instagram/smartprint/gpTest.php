<?php
/*************************************************************************************************************************
GET_PHOTOS

Retorna una llista de totes les fotografies per un hashtag/username en forma de URL.

Endpoint API/instagram/smartprint/get_photos.php

Input
w, paraula a cercar en hastags i usernames
Output
nombre de fotografies trobades per hashtag 
nombre de fotografies trobades per username
llista d’URLs de cada fotografia, URLs, extensió, data descarrega i numeroLikes separades per ‘|’. Per exemple: url1|jpg|data_descarrega1|1100|url2|jpg|data_descarrega 5000
Les URLs estan endreçades per numero de likes, prints i visualitzacions i es llisten primer totes les de hashtag i a continuació totes les de username.
***************************************************************************************************************************/
error_reporting(0);
ini_set('display_errors', 1);

use instagram\smartprint\domain\InstagramUser;
// use facebook\smartprint\infrastructure\PDOFacebookRepository;

require_once "domain/InstagramUser.php";

use instagram\smartprint\infrastructure\PDOInstagramRepository;

require_once "infrastructure/PDOInstagramRepository.php";


require_once "config/ig_config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/global.php';
require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/common/conexio.php';

// require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'; // change path as needed

/*************************************************************************************************************************
****************************************************************************************************************
//TODO: modificacions v2: 
-----afegir: data descarrega(si la tibem de les guardades), numero likes
-----Ha de tornar al final de cada tipus (usuari/hashtag) totes les fotos que haurem guardat a la BD i en carpeta del server per aquella paraula
***********************************************************************************************************/



try {
    $igRepository = new PDOInstagramRepository();    
    utils::log("get_photos starts", LOG_FILE);
    guardAgainstMissingParameters(); //TODO: afegeix parametres que falten

//---fem control de token i mirarem si ja les hem servit totes
//-------hem de mostrar només les descarregades ja a MPC    
    
    $igUser = $igRepository->findByToken($_GET['tkn']);   



    if(empty($igUser->getAccessToken())){
        echo "KO";
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];

        $photos_number = $_GET['n'];
        $token = $_GET['tkn'];
        $igUser = getIGUser(
            $token,
            $_GET['id']
        );
        $type = $_GET['typ'];
        $other = $_GET['other'];

      
        if($igUser->getAllPhotosServed() == 1){
            echo "OK#0";
            exit;
        }

        
        if(!isset($_GET['w'])){
           
         $word = ''; 
        }else{          
         $word = $_GET['w']; //nom carpeta i arxiu .txt que guarda les fotos es la propia paraula. Cal canviar-ho?
        }
        
        

        $filename = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$word/$word.txt"; //pot ser timestamp o similar
       
   

        $urlsArray = getNextPhotos($word, $filename, $token, $type, $igUser->getLastDownloadedPhotos(), $photos_number, $photos_number, $other); 
// print "<pre>";
//         print_r($urlsArray);
        $urls = $urlsArray['urls'];
        $urlsDownloaded = $urlsArray['urlsDownloaded'];
//         print $urlsDownloaded;

        if(isset($urls) && $urls != "" ){
            downloadNextPhotosInBackground($word,$urls, $token, $ip);           
            $result = "OK#$urlsDownloaded";
        } else {
            $result = "OK#0";
        }
        
    }




    
    
    utils::log("get_photos $result", LOG_FILE);
    echo $result;
} catch( Exception $e){    
    utils::log("get_photos " . getInputToken() . " error: ". $e->getMessage(), LOG_FILE);
    echo "KO#{$e->getMessage()}";
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
        if (!array_key_exists('code', $_GET) || !isset($_GET['code']) || $_GET['code'] == "") {
            throw new Exception("Missing game code");
        }
        if (!array_key_exists('tkn', $_GET) || !isset($_GET['tkn'])) {
            throw new Exception("Missing token");
        }
        if (!array_key_exists('typ', $_GET) || !isset($_GET['typ'])) {
            throw new Exception("Missing typ");
        }
        if (!array_key_exists('other', $_GET) || !isset($_GET['other'])) {
            throw new Exception("Missing other");
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

// function getFBUser($token, $pbId) {
//     global $igRepository;

//     return $igRepository->findByTokenAndIdBooth($token, $pbId);
// }

function updateLastPhoto($token, $last_photo){
    global $igRepository;

    $igRepository->updateLastPhoto($token, $last_photo);
}

 function scrape_insta_user_images($username) {
//    login_inst();
//    sleep(5);
//    $page = curl_inst('https://www.instagram.com/explore/tags/stackoverflow/');
    
//    print_r($page);
//    exit;
    
//$insta_source = file_get_contents($page); // instagram user url
     
//    $insta_source = json_encode($page);
//    $insta_source = $page;
//    print $insta_source;
//    exit;
        
       
   
    //ITS PROXY TIME!!
    $ch = curl_init();

    // set url
     
    //Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP fins 15 Agost 1000 credits
     // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $99 per 1000000/mes a https://app.scrapingbee.com/
     //test nou amb scinstapi@gmail.com // digital36
$tag = $username;
    curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&premium_proxy=true&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F');

//provem amb un altre proxy aquest es mes barato jperia $29
//curl "https://api.webscraping.ai/html?api_key=169a6f9b-3513-45e3-a830-ad0ab3dd0bff&url=https://example.com"
//curl_setopt($ch, CURLOPT_URL, 'https://api.webscraping.ai/html?api_key=169a6f9b-3513-45e3-a830-ad0ab3dd0bff&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F');
    // set method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 // send the request and save response to $insta_source
    $insta_source = curl_exec($ch);
        
    
    
    
        
	$shards = explode('window._sharedData = ', $insta_source);
	$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	return $insta_array; // this return a lot things print it and see what else you need
}


function getNextPhotos($word, $filename, $token, $typeRequest, $numFrom, $numTo, $photos_number, $other=0) {
    global $igRepository;
    $urls = "";
    $urlsDownloaded = "";
    $urlsDownloaded2 = "";
    $arrayUrls = array();
    $arrayDownloadedPhotosToAdd = array();
    // $photos_number = ($fbUser->getLastDownloadedPhotos() > 0) ?
    //     $fbUser->getLastDownloadedPhotos() :
    //     $fbUser->getNumPhotos();
    //$photos_number = 0;
    $numPhotosHashDownloaded = 0;     
    $numPhotosUserDownloaded = 0;  
    $ip = $_SERVER['REMOTE_ADDR'];

    $pais = getCountryByIp($ip);  

    
//  $word = $_GET['w'];
//       $results_array = scrape_insta_user_images($word);
//echo '<pre>';
//print_r($results_array);
//echo '<pre>';
//    
//    
//    exit;  
//    
    
    
    
    $results_hash_array = scrape_insta_hash($word);
//    print $word."<pre>";
//    print_r($results_hash_array);
//    exit;
    
    $results_user_array = scrape_insta_user($word);
//    print_r($results_user_array);
    $image_hash_array_likes = array();
    $image_user_array_likes = array();
    $fp = createImagesDirectory($word);
    
    $photos_number_hashtag = count($results_hash_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges']);
    //Afegim les de hashtag
    for ($i=0; $i < $photos_number_hashtag; $i++) { 
        if($results_hash_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'][$i]['node']['is_video']!=1){
        
            $latest_hash_array = $results_hash_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'][$i]['node'];    
            
            if ($i > 0) {
                    $urls .= "|";
                }
            //$urls .= $latest_user_array['thumbnail_resources'][4]['src'];
            $urls .= PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto=".$latest_hash_array['id']."&w={$word}&ext=jpg&tkn={$token}|jpg|".$latest_hash_array['edge_liked_by']['count'];

             //$latest_user_array['edge_liked_by']['count'].$latest_user_array['id'] --> concatenem els likes al id perque volem endreçar l'array de mes likes a menys però no volem que si 2 imatges tenen el mateix numero de likes una sobrescrigui l'altra.

            //$image_hash_array_likes[$latest_hash_array['edge_liked_by']['count'].$latest_hash_array['id']]['image_data'] = $image_data;
            $image_hash_array_likes[$latest_hash_array['edge_liked_by']['count'].$latest_hash_array['id']]['id'] = $latest_hash_array['id'];
            $image_hash_array_likes[$latest_hash_array['edge_liked_by']['count'].$latest_hash_array['id']]['image_url'] = $latest_hash_array['thumbnail_resources'][4]['src'];
            $image_hash_array_likes[$latest_hash_array['edge_liked_by']['count'].$latest_hash_array['id']]['image_likes_count'] = $latest_hash_array['edge_liked_by']['count'];
            $image_hash_array_likes[$latest_hash_array['edge_liked_by']['count'].$latest_hash_array['id']]['image_text'] = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $latest_hash_array['edge_media_to_caption']['edges'][0]['node']['text']);
// $onelineString = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $latest_hash_array['edge_media_to_caption']['edges'][0]['node']['text']);
// print $onelineString;

// function removeEmptyLines($s) {
// return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $s);
// }
           // array_push($image_hash_array, $image_data);
            krsort($image_hash_array_likes);
            
            
        }
    }


    $photos_number_hashtag = count($image_hash_array_likes); //les reals, sense video
    
    if($typeRequest=='hashtag'){
        $totalPhotos = $photos_number_hashtag;
       
    }
    foreach ($image_hash_array_likes as $image) {
      
       
        //escrivim id de la foto            
        fputs($fp, "{$image['id']}|{$image['image_url']}|{$image['image_likes_count']}\n"); 
         //$type = "username";
         

            

        //Guardem les photos a BD i despres les marcarem com a downloaded desde download_photos.php
        $type = "hashtag"; 
        $id_photo = $image['id'];
        $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$word}/$id_photo.jpg";
        $numLikes = $image['image_likes_count'];
        $text =  $image['image_text'];  
        $existsPhoto = getPhotoByIdCountryType($id_photo, $pais, $type);
       // print "existeix".$existsPhoto;
        if(!$existsPhoto){
            saveIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, $text);            
         }else{
            updateIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, 0, 1, 0);
        }           
    }
 
   
 $photos_number_username = count($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges']);  
 
    for ($i=0; $i < $photos_number_username; $i++) { 
        if(isset($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]) && $results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]['node']['is_video']!=1 && !empty($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'])){
        
            $latest_user_array = $results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]['node'];         
            
            if ($i > 0) {
                    $urls .= "|";
                }
                // print "<pre>";
                // print_r($latest_user_array);
            if(isset($latest_user_array['id']) && $latest_user_array['id']!=''){
                 //$urls .= $latest_user_array['thumbnail_resources'][4]['src'];
            $urls .= PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto=".$latest_user_array['id']."&w={$word}&ext=jpg&tkn={$token}|jpg|".$latest_user_array['edge_liked_by']['count'];

            //Concatenem els likes al id perque volem endreçar l'array de mes likes a menys però no volem que si 2 imatges tenen el mateix numero de likes una sobrescrigui l'altra:
            //$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']

           // $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_data'] = $image_data;
            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['id'] = $latest_user_array['id'];
            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_url'] = $latest_user_array['thumbnail_resources'][4]['src'];
            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_likes_count'] = $latest_user_array['edge_liked_by']['count'];

            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_text'] = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $latest_user_array['edge_media_to_caption']['edges'][0]['node']['text']);

            // array_push($image_user_array, $image_data);
            krsort($image_user_array_likes);

            }    
           
            
            
        }
    }
 
    $photos_number_username = count($image_user_array_likes); //les reals, sense video
    if($typeRequest=='username'){
        $totalPhotos = $photos_number_username;
        
    }

    foreach ($image_user_array_likes as $image) {
    
        
        //escrivim id de la foto     
        //Preparem fitxer de descarrega en background   
        fputs($fp, "{$image['id']}|{$image['image_url']}|{$image['image_likes_count']}\n");    
        //Guardem les photos a BD i despres les marcarem com a downloaded desde download_photos.php
        $type = "username"; 
        $id_photo = $image['id'];
        $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$word}/$id_photo.jpg";
        $numLikes = $image['image_likes_count'];
        $text =  $image['image_text'];  
        $existsPhoto = getPhotoByIdCountryType($id_photo, $pais, $type);
        //print "existeix".$existsPhoto;
        if(!$existsPhoto){
            saveIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, $text);            
         }else{
            updateIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, 0, 1, 0);
        }   
    }
   


    fclose($fp); 



    // 1 ----- SELECT
    $arrayDownloadedPhotos = getSavedInstaPhotos($word, $typeRequest, $pais, $numFrom, $numTo, $other); 

    
    //Si $lastPhotosServed<$photos_number ha d'agafar les de tots els paisos
    //repetirem la linea anterior passant parametre pais=''. A la funció, si país = '' els retorna tots.
    $lastPhotosServed = count($arrayDownloadedPhotos);
    if($lastPhotosServed<$photos_number){
        $pais='';
        $arrayDownloadedPhotos = getSavedInstaPhotos($word, $typeRequest, $pais, $numFrom, $numTo, $other); 
        // print_r($arrayDownloadedPhotos);
        $lastPhotosServed = count($arrayDownloadedPhotos);
    }

  // print_r($arrayDownloadedPhotos);

   
    $numPhotosDownloaded = $lastPhotosServed; 
   

    foreach($arrayDownloadedPhotos as $imageDownloaded){
        $urlsDownloaded .= PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto=".$imageDownloaded['id']."&w=".$imageDownloaded['word']."&ext=jpg&tkn={$token}|jpg|".$imageDownloaded['numLikes']."|".$imageDownloaded['id']."|";
    }

   
    
    updateDownloadedPhotos($token, $lastPhotosServed);
   
    
    utils::log("getNextPhotos: photos_number: $photos_number", "eloi");
    utils::log("getNextPhotos: arrayDownloadedPhotos: $arrayDownloadedPhotos", "eloi");
   
    if($urls != "") {
        $arrayUrls['urls'] = "$photos_number_hashtag#$photos_number_username#$urls";
    }
print "urlsDownloaded: ".$urlsDownloaded;
print_r($arrayDownloadedPhotos);  
    if($urlsDownloaded != "") {
        $urlsDownloaded = substr($urlsDownloaded, 0, -1); //eliminem l'ultim |
        $arrayUrls['urlsDownloaded'] = "$numPhotosDownloaded#$urlsDownloaded";
    }
    $totalPhotosInDB = getSavedInstaPhotosNumber($word, $typeRequest, $pais, $other); 
    
     updateTotalPhotos($token, $totalPhotosInDB);
     // print $lastPhotosServed.">=".$totalPhotosInDB;
    $igUser = $igRepository->findByToken($token); 


    //print $igUser->getLastDownloadedPhotos().$igUser->getTotalPhotos();
    if($igUser->getLastDownloadedPhotos()>=$igUser->getTotalPhotos() && $totalPhotosInDB!=0){
         updateAllPhotosServed($token, 1); 
    }

    //TODO: potser no cal perque ja les tenim descarregades, no? Això seria per si les fessim de mica en mica
    // downloadNextPhotosInBackground($word, $urls, $token, $ip);     
   // print_r($arrayUrls);
    return $arrayUrls;
}




function createImagesDirectory($token){
    $fp = NULL;
    if(isset($token)){
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token");
        }
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        //Descomantar per a acumular totes les imatges que hem anat descarregant
        //if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token/$token.txt")) {
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "w"); //el crea de nou, elimina contingut
        //}else{
          
            //$fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "a+"); //no elimina el contingut, afegeix al final.
        //}  
        var_dump($fp);
            print $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token";
    }
    return $fp;
}

function downloadNextPhotosInBackground($word,$urls,$token,$ip){

    $countTypes = explode("#", $urls);
    
    $numuser = $countTypes[1];
    $numhash = $countTypes[0];
// print "-------------------------------------------------------\n";
//     print "/API/instagram/smartprint/download_photos.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&ip={$ip}";
//     print "-------------------------------------------------------\n";
    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/instagram/smartprint/download_photos.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&ip={$ip}\" >>".$_SERVER['DOCUMENT_ROOT']."/myphotocode/log/numa.dat  2>&1 &";    
    utils::log("downloadNextPhotos $command", "eloi");
    exec($command);
    
}

//SCRAPE INSTA
function scrape_insta_user($username) {
//    login_inst();
//    sleep(5);
    // $insta_source = file_get_contents('https://www.instagram.com/'.$username.'/'); // instagram user url
    $ch = curl_init();

    // set url 
    //EJJOPIV08JNTN8UY2S7E8CY5WLRQT51E0EM1E4F1F3OYLBJKCNMV5JFU3EJ0H8R9GXA99QJ8RJ1HMW99 fins 19 setembre, queden 800 credits
    //RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G fins 21 setembre 5000 credits eloi@dc-image.com
    //Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP fins al 15 Agost 1000 credits
    // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $29 per 250000/mes a https://app.scrapingbee.com/
    //curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&premium_proxy=true&url=https%3A%2F%2Fwww.instagram.com%2F'.$username.'%2F');
    curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&url=https%3A%2F%2Fwww.instagram.com%2F'.$username.'%2F&premium_proxy=true&country_code=us');

    // set method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



    // send the request and save response to $insta_source
    $insta_source = curl_exec($ch);

    // stop if fails
    if (!$insta_source) {
      // die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
      echo "KO";
      exit;  
    }

    // echo 'HTTP Status Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
    // echo 'Response Body: ' . $response . PHP_EOL;

    // close curl resource to free up system resources
    curl_close($ch);

    $shards = explode('window._sharedData = ', $insta_source);
    $insta_json = explode(';</script>', $shards[1]); 
    $insta_array = json_decode($insta_json[0], TRUE);
    return $insta_array; // this return a lot things print it and see what else you need
}




function scrape_insta_hash($tag) {
    
    
     
    
//    login_inst();
//    sleep(5);
    // $insta_source = file_get_contents('https://www.instagram.com/explore/tags/'.$tag.'/'); // instagrame tag url
     $ch = curl_init();

    // set url
     //EJJOPIV08JNTN8UY2S7E8CY5WLRQT51E0EM1E4F1F3OYLBJKCNMV5JFU3EJ0H8R9GXA99QJ8RJ1HMW99 fins 19 setembre, queden 800 credits
    //Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP fins 15 Agost 1000 credits
     // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $99 per 1000000/mes a https://app.scrapingbee.com/
     //test nou amb scinstapi@gmail.com // digital36
                                // https://app.scrapingbee.com/api/v1/?api_key=Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP&premium_proxy=true&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F'
    //curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&premium_proxy=true&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F');
    curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F&premium_proxy=true&country_code=us');

    // set method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



    // send the request and save response to $insta_source
    $insta_source = curl_exec($ch);

    // stop if fails
    if (!$insta_source) {
      echo "KO";
      exit;  
    }

    // echo 'HTTP Status Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
    // echo 'Response Body: ' . $response . PHP_EOL;

    // close curl resource to free up system resources
    curl_close($ch);

    $shards = explode('window._sharedData = ', $insta_source);
    $insta_json = explode(';</script>', $shards[1]); 
    $insta_array = json_decode($insta_json[0], TRUE);
    return $insta_array; // this return a lot things print it and see what else you need
}

function login_inst() {

    @unlink(dirname(__FILE__)."/".COOKIE);

    $url="https://www.instagram.com/accounts/login/?force_classic_login";

    $ch  = curl_init(); 

    $arrSetHeaders = array(
        "User-Agent: USERAGENT",
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: deflate, br',
        'Connection: keep-alive',
        'cache-control: max-age=0',
    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);         
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $page = curl_exec($ch);
    curl_close($ch);  

//    var_dump($page);

    // try to find the actual login form
//    if (!preg_match('/<form method="POST" id="login-form" class="adjacent".*?<\/form>/is', $page, $form)) {
//        die('Failed to find log in form!');
//    }
   
     if (!preg_match('/<form data-encrypt method="POST" id="login-form" class="adjacent".*?<\/form>/is', $page, $form)) {
        die('Failed to find log in form!');
    }
//    if (!preg_match('/<form data-encrypt method="POST" id="login-form" class="adjacent".*?</form>/is', $page, $form)) {
//        die('Failed to find log in form!');
//    }

    $form = $form[0];

    // find the action of the login form
    if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
        die('Failed to find login form url');
    }

    $url2 = $action[1]; // this is our new post url
    // find all hidden fields which we need to send with our login, this includes security tokens
    $count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);

    $postFields = array();

    // turn the hidden fields into an array
    for ($i = 0; $i < $count; ++$i) {
        $postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
    }

    // add our login values
    $postFields['username'] = USERNAME;
    $postFields['password'] = PASSWORD;   

    $post = '';

    // convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
    foreach($postFields as $key => $value) {
        $post .= $key . '=' . urlencode($value) . '&';
    }

    $post = substr($post, 0, -1);   

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $page, $matches);

    $cookieFileContent = '';

    foreach($matches[1] as $item) 
    {
        $cookieFileContent .= "$item; ";
    }

    $cookieFileContent = rtrim($cookieFileContent, '; ');
    $cookieFileContent = str_replace('sessionid=""; ', '', $cookieFileContent);

    $oldContent = file_get_contents(dirname(__FILE__)."/".COOKIE);
    $oldContArr = explode("\n", $oldContent);

    if(count($oldContArr))
    {
        foreach($oldContArr as $k => $line)
        {
            if(strstr($line, '# '))
            {
                unset($oldContArr[$k]);
            }
        }

        $newContent = implode("\n", $oldContArr);
        $newContent = trim($newContent, "\n");

        file_put_contents(
            dirname(__FILE__)."/".COOKIE,
            $newContent
        );    
    }

    $arrSetHeaders = array(
        'origin: https://www.instagram.com',
        'authority: www.instagram.com',
        'upgrade-insecure-requests: 1',
        'Host: www.instagram.com',
        "User-Agent: USERAGENT",
        'content-type: application/x-www-form-urlencoded',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: deflate, br',
        "Referer: $url",
        "Cookie: $cookieFileContent",
        'Connection: keep-alive',
        'cache-control: max-age=0',
    );

    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);     
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    sleep(5);
    $page = curl_exec($ch);

    /*
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $page, $matches);
    COOKIEs = array();
    foreach($matches[1] as $item) {
        parse_str($item, COOKIE1);
        COOKIEs = array_merge(COOKIEs, COOKIE1);
    }
    */
    //var_dump($page);      
    curl_close($ch);  

}

function getSavedInstaPhotos($word='',$type='', $pais, $numFrom, $numTo, $other){
    //TODO: podem fer una consulta que si li pasem $tag, retorna totes les del tag, si li passem $type filtra per type i si li passem un num limita les que retorna.
    global $igRepository;

    return $igRepository->getSavedInstaPhotos($word, $type, $pais, $numFrom, $numTo, $other);
}

function getSavedInstaPhotosOther($numberPhotosOtherToAdd){
    
    global $igRepository;

    return $igRepository->getSavedInstaPhotosOther($numberPhotosOtherToAdd);
}



function getSavedInstaPhotosNumber($word='',$type='', $pais, $other){
    
    global $igRepository;

    return $igRepository->getSavedInstaPhotosNumber($word, $type, $pais, $other);
}

function saveIGPhotoDB($id_photo, $word, $path, $type, $pais, $numLikes, $text){
    //TODO: Aqui un select a InstagramPhotoViewed per id=idPhoto
    //1.- si exiteix incrementem +1 numcount
    //2.- si no existeix guarda id=$id_photo, numPrint=0, numCount=1, word=$word, type=$type, path=$path, downloadDate=, numlikes

    //3.-com controlarem quines enviem a cada petició segons el numero que ens en demanin??
    global $igRepository;

    return $igRepository->insertPhotoViewed($id_photo, $word, $path, $type, $pais, $numLikes, $text);
}

function updateIGPhotoDB($id_photo, $word, $path, $type, $pais, $numLikes, $numPrint, $numCount){
    
    global $igRepository;

    return $igRepository->updatePhotoViewed($id_photo, $word, $path, $type, $pais, $numLikes, $numPrint, $numCount);
}

// function getPhotoById($id_photo) {
//     global $igRepository;

//     return $igRepository->getPhotoById($id_photo);
// }
function getPhotoByIdCountryType($id_photo, $pais, $type) {
    global $igRepository;

    return $igRepository->getPhotoByIdCountryType($id_photo, $pais, $type);
}
function getCountryByIp($ip) {
    global $igRepository;

    return $igRepository->getCountryByIp($ip);
}

function getIGUser($token, $pbId) {
    global $igRepository;

    return $igRepository->findByTokenAndIdBooth($token, $pbId);
}

//Potser no cal, si fem un LIMIT from, to ja ens retornarà nomes les que hi hagi
function getNextPhotoIndex(InstagramUser $fbUser, $photos_number){
    global $igRepository;

    $index = 0;
    // $num_photos_file = count($fileContent);
    // if(is_array($fileContent) && isset($fileContent)){
    //     foreach($fileContent as $line){
    //         if(str_replace(PHP_EOL, '', $line) == $igUser->getLastPhoto()){
    //             break;
    //         }
    //         $index++;
    //     }
    // }
    // if($index==$num_photos_file-1){
    //     $fbRepository->updateAllPhotosServed($fbUser->getToken(), 1);
    // }

    return $index - ($photos_number-1);
}

// function updateUserTotalPhotos($token,$totalLines){
//     global $igRepository;
    
//     if($totalLines>0) {
//         $igRepository->updateUserTotalPhotos($token, $totalLines);
//     }
// }

function updateTotalPhotos($token, $totalPhotos){
    
    global $igRepository;

    return $igRepository->updateTotalPhotos($token, $totalPhotos);
}

function updateDownloadedPhotos($token, $lastPhotosServed){
    
    global $igRepository;

    return $igRepository->updateDownloadedPhotos($token, $lastPhotosServed);
}

function updateAllPhotosServed($token, $value){
    
    global $igRepository;

    return $igRepository->updateAllPhotosServed($token, $value); 
}


