<?php
/*************************************************************************************************************************
Retorna una llista de totes les fotografies per un hashtag/username en forma de URL 
ordenades de la més recent a la més antiga.
Es demanaràn desde PB les fotos per una paraula i tipus cada vegada. 

Les paraules per les que anirà demanant fotos són les que tingui del que ha descarregat prèviament de GET_SUGGESTION_BACKGROUND

Es retornarà un llistat amb totes les fotos possibles per paraula i tipus i el número de fotos a descarregar ordenades per data.


Endpoint API/instagram/smartprint/get_photos_background.php


Input
    w, paraula a cercar en hashtags o usernames
    typ, filtre (valors: hashtag, username) Paràmetre obligatori.


Output
    nombre de fotografies trobades
    nombre de fotografies a descarregar
    llista d’URLs de cada fotografia, URLs, extensió, data descarrega, numeroLikes, identificador de la foto, phototext separades per ‘|’. Per exemple: url1|jpg|data_descarrega1|1100|2319670675609479881|text_base64_encode |url2|jpg|data_descarrega|5000|2315338618804397539|text_base64_encode
     * Les URLs estan endreçades per data, la més recent primer.
     * phototext: Text de la foto urlencoded
    Camps de perfil usuari si hi ha un usuari que coincideixi amb la paraula cercada (si no hi ha coincidència tornem 0) separats per ‘|’:
     * url a la fotoperfil
     * verified: 0 no és usuari verificat, 1 és usuari verificat 
     * numfollowers

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
    utils::log("get_photos_background starts", LOG_FILE);
    guardAgainstMissingParameters(); //TODO: afegeix parametres que falten

   
        $type = $_GET['typ'];
        $token = $_GET['tkn'];
        
        $ip = $_SERVER['REMOTE_ADDR'];
//        if(!isset($_GET['n'] || $_GET['n']==''){
//            $photos_number = $_GET['n'];
//        }else{
//            $photos_number = $_GET['n'];
//        }
//        
        
      
        
       

        

        
        if(!isset($_GET['w'])){
           
         $word = ''; 
        }else{          
         $word = $_GET['w']; //nom carpeta i arxiu .txt que guarda les fotos es la propia paraula. Cal canviar-ho?
        }
        
        

        $filename = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$word/$word.txt"; //pot ser timestamp o similar

   
        $urlsArray = getNextPhotos($word, $filename, $token, $type, $numFrom , $photos_number, $photos_number, 0, 0); 
// print "++++++urlsarray<pre>";
// print_r($urlsArray);
        $urls = $urlsArray['urls'];
        $urlsDownloaded = $urlsArray['urlsDownloaded'];
        $profileVars = $urlsArray['profileVars'];
//print "+-+-+-+-+-+urlsDownloaded<pre>";
//print $urlsDownloaded;

        
            
        $result = "OK#48#$urlsDownloaded#$profileVars";
           
    
    utils::log("get_photos_background $result", LOG_FILE);
    echo $result;
} catch( Exception $e){    
    utils::log("get_photos_background " . getInputToken() . " error: ". $e->getMessage(), LOG_FILE);
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
       
        if (!array_key_exists('typ', $_GET) || !isset($_GET['typ'])) {
            throw new Exception("Missing typ");
        }
       
        if (array_key_exists('w', $_GET) && !isset($_GET['w'])) {
            throw new Exception("Invalid word");
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

function getNextPhotos($word, $filename, $token, $typeRequest, $numFrom, $numTo, $photos_number, $other=0, $scrap=1) {
    
    
    global $igRepository;
    $urls = "";
    $urlsDownloaded = "";
    $urlsDownloaded2 = "";
    $fbid = 0;
    $isVerified = 0;
    $numFollowers = 0;
    $profileVars = 0;
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
    
    //si no es la primera peticio que fa per aquest token no cal tornar a fer crida a Instagram consumint credits
    if($numFrom==0 && $scrap==1){
        
         if($typeRequest=="hashtag"){
            $results_hash_array = scrape_insta_hash($word);
            $results_user_array = array();
        }else{
            $results_user_array = scrape_insta_user($word);
            $results_hash_array = array();
        }
        //ABANS ELS FEIEM TOTS INDEPENDENTMENT DEL QUE DEMANESSIN PER ANAR OMPLINT LA BD. ÉS INASSUMIBLE PER COSTOS, LES CRIDES SÓN MOLT CARES I A MÉS DONA TIMEOUTS.
        //TAMBÉ GUANYAREM TEMPS DE RESPOSTA
//                    $results_hash_array = scrape_insta_hash($word);
//                    $results_user_array = scrape_insta_user($word);
               
                   

                //    print "<pre>";
                //    print_r($results_hash_array) ;
                //    print "-----user array scrap----";
                //    print_r($results_user_array) ;
                //    exit;



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




                 if($typeRequest=='username' && isset($results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['fbid'])){

                     $fbid = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['fbid'];
                     $numFollowers = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['edge_followed_by']['count'];  
                     $isVerified = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['is_verified'];
                     if($isVerified==''){
                        $isVerified = 0; 
                     }
                    // print $fbid;
                     $fp_fbid = createImageProfileDirectory($fbid);  
                    fputs($fp_fbid, "{$results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['profile_pic_url_hd']}\n"); 
                    downloadPhotoProfileInBackground($fbid,$token);

                            // PHOTO_DOMAIN . IMAGES_PATH . "/{$fbid}/$fbid.jpg"; //també funcionaria
                    $fbidImg =  PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto={$fbid}&w={$fbid}&ext=jpg&tkn={$token}";
                    $profileVars = $fbidImg."|".$isVerified."|".$numFollowers;





                 }else{
                     $fbid = 0;
                     $profileVars = 0;
                 }   

                 $photos_number_username = count($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges']);  
                    //afegim les de username 
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

                            //$image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_data'] = $image_data;
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['id'] = $latest_user_array['id'];
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_url'] = $latest_user_array['thumbnail_resources'][4]['src'];
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_likes_count'] = $latest_user_array['edge_liked_by']['count'];

                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['image_text'] = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $latest_user_array['edge_media_to_caption']['edges'][0]['node']['text']);

                            //afegim camps user profile 
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['numFollowers'] = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['edge_followed_by']['count'];                                               
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['fbid'] = $fbid;     
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['is_verified'] = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['is_verified'];
                            //$image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['profile_pic_url_hd'] = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['profile_pic_url_hd']; // [profile_pic_url]                                            
                            $image_user_array_likes[$latest_user_array['edge_liked_by']['count'].$latest_user_array['id']]['username'] = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['username']; 

                            // array_push($image_user_array, $image_data);
                            krsort($image_user_array_likes);

                            }    



                        }
                    }

                    $photos_number_username = count($image_user_array_likes); //les reals, sense video
                    if($typeRequest=='username'){
                        $totalPhotos = $photos_number_username;

                    }


                    //IMPORTANT: posem primer les de user perquè n'hi haurà menys i així en les peticions de user no hem d'esperar que es baixin totes les de hashtag per a poder retornar-les
                    //si les posem primer al fitxer, desde download_photos.php les descarregarà abans
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
                            saveIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, $text, $fbid);            
                         }else{
                            updateIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, 0, 1, 0, $text);
                        }   
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
                            saveIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, $text, 0);            
                         }else{
                            updateIGPhotoDB($id_photo, $word, $img, $type, $pais, $numLikes, 0, 1, 0, $text);
                        }           
                    }



                    fclose($fp); 

                //TODO:treure quan volguem filtrar resultats per país. No funcionarà, potser... mira les consultes de getSavedInstaPhotos a veure que falla quan pais!=''
                    //$pais=''; 
                //    print $numFrom;
                    // 1 ----- SELECT
                    
                    
//                    sleep(10);

    }                
 //--------------------------------------fins aqui scrapingbee, no el fem si numFrom > 0 ---------------------------------------------------------------   
    
    
  
        //deixem parametre pais per si volguessim filtrar en un futur. L'agafariem de la ip
        $paisBuit='';
        $arrayDownloadedPhotosValues = getSavedInstaPhotosAll($word, $typeRequest, $paisBuit); 
        $arrayDownloadedPhotos = $arrayDownloadedPhotosValues['row'];

//print_r($arrayDownloadedPhotosValues);
   
    $numPhotosDownloaded = $arrayDownloadedPhotosValues['rowCount'];; 
   
    $countFbid = 0;
    foreach($arrayDownloadedPhotos as $imageDownloaded){
        $urlsDownloaded .= PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto=".$imageDownloaded['id']."&w=".$imageDownloaded['word']."&ext=jpg&tkn={$token}|jpg|".$imageDownloaded['numLikes']."|".$imageDownloaded['id']."|".base64_encode($imageDownloaded['photoText'])."|";
        
        //Si no es la primera petició, en que fem scraping cal afegir el perfil de usuari si es el tenim
        if(!$countFbid && $imageDownloaded['fbid'] && !$profileVars){ //no n'hem trobat cap i fbid no es 0. I no hem assignat $profileVars quan fem scraping
           $fbid = $imageDownloaded['fbid'];
           if(file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$fbid}/$fbid.jpg")) {
               $fbidImg =  PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto={$fbid}&w={$fbid}&ext=jpg&tkn={$token}";
               $profileData = findUsernameByWord($imageDownloaded['word']);               
               $isVerified = $profileData['isVerified'];
               $numFollowers = $profileData['numFollowers'];
               $profileVars = $fbidImg."|".$isVerified."|".$numFollowers;  
           }
           $countFbid = 1;
            
        }
    }
    
    

    
   
    
//    utils::log("getNextPhotos: Background: photos_number: $photos_number", "eloi");
   
    if($urls != "") {
        $arrayUrls['urls'] = "$photos_number_hashtag#$photos_number_username#$urls";
        $arrayUrls['profileVars'] = $profileVars;

    }
   
  
    if($urlsDownloaded != "") {
        $urlsDownloaded = substr($urlsDownloaded, 0, -1); //eliminem l'ultim |
//        $arrayUrls['urlsDownloaded'] = "$numPhotosDownloaded#$urlsDownloaded#$profileVars";
        $arrayUrls['urlsDownloaded'] = "$numPhotosDownloaded#$urlsDownloaded"; //afegirem profileVars fora de la funció perquè no sempre els tornem i la cridem varies vegades per si no hem tingut temps de descarregar imatges i millorar el rendiment
        $arrayUrls['profileVars'] = $profileVars;
        //Guardem a suggestion si ha retornat alguna foto
        //per comprovar si existeix... si no actualitzarem el suggestion
        
        $countMatches = getWord(
            $word,        
            $typeRequest,
            $pais
        );
        //omplim de dades o actualitzem els que ja haguem insertat desde Myphotocode apartat Instagram 
        //o guardat desde get_suggestions on no fem crida a scrapingbee per estalviar i per tant no omplim els camps
         $action = updateSuggestion( 
                        $word,                       
                        $typeRequest,                        
                        $numFollowers,
                        $isVerified,
                        $fbid
                    );
         
        if(!$countMatches){  
            
            
             $action = insertSuggestion( 
                            $word,
                            0,
                            $typeRequest,
                            $pais,
                            $numFollowers,
                            $isVerified,
                            $fbid
                        ); 

       
            
           
        }
    }
    $totalPhotosInDB = getSavedInstaPhotosNumber($word, $typeRequest, $pais, $other); 
    
    updateTotalPhotos($token, $totalPhotosInDB);
    // print $lastPhotosServed.">=".$totalPhotosInDB;
    $igUser = $igRepository->findByToken($token); 


    //print $igUser->getLastDownloadedPhotos().$igUser->getTotalPhotos();
    if($igUser->getLastDownloadedPhotos()>=$igUser->getTotalPhotos() && $totalPhotosInDB!=0){
         updateAllPhotosServed($token, 1); 
    }

    
    return $arrayUrls;
}



function updateSuggestion($word, $type, $numFollowers, $isVerified, $fbid) {
    global $igRepository;

    return $igRepository->updateSuggestion($word, $type, $numFollowers, $isVerified, $fbid);
}

function getWord($word, $type, $pais) {
    global $igRepository;

    return $igRepository->findByWord($word, $type, $pais);
}

function findUsernameByWord($word) {
    global $igRepository;

    return $igRepository->findUsernameByWord($word);
}

function insertSuggestion($word, $print, $type, $pais, $numFollowers, $isVerified, $fbid) {
    global $igRepository;

    return $igRepository->insertSuggestion($word, $print, $type, $pais, $numFollowers, $isVerified, $fbid);
}



function createImagesDirectory($token){
    $fp = NULL;
    if(isset($token)){
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token");
        }
        //Descomantar per a acumular totes les imatges que hem anat descarregant
        //if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token/$token.txt")) {
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "w"); //el crea de nou, elimina contingut
        //}else{
          
            //$fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "a+"); //no elimina el contingut, afegeix al final.
        //}    
    }
    return $fp;
}

function createImageProfileDirectory($fbid){
    $fp_fbid = NULL;
    if(isset($fbid)){
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$fbid")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$fbid");
        }
        //Descomentar per a acumular totes les imatges que hem anat descarregant
        //if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token/$token.txt")) {
            $fp_fbid = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$fbid/$fbid.txt", "w"); //el crea de nou, elimina contingut
        //}else{
          
            //$fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "a+"); //no elimina el contingut, afegeix al final.
        //}    
    }
    return $fp_fbid;
}

function downloadNextPhotosInBackground($word,$urls,$token,$ip){

    $countTypes = explode("#", $urls);
    
    $numuser = $countTypes[1];
    $numhash = $countTypes[0];
// print "-------------------------------------------------------\n";
    // print "/API/instagram/smartprint/download_photos.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&ip={$ip}";
//     print "-------------------------------------------------------\n";
    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/instagram/smartprint/download_photos.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&ip={$ip}\" >>".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat  2>&1 &";    
    utils::log("downloadNextPhotos $command", "eloi");
    exec($command);
    
}

function downloadPhotoProfileInBackground($fbid,$token){ 
  

    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/instagram/smartprint/download_photo_profile.php?fbid={$fbid}&tkn={$token}\" >>".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat  2>&1 &";    
    utils::log("downloadPhotoProfile $command", "eloi");
    exec($command);
    
}

//SCRAPE INSTA
function scrape_insta_user($username) {
    // $insta_source = file_get_contents('https://www.instagram.com/'.$username.'/'); // instagram user url
    $ch = curl_init();

    // set url 
    // //junovarsb@gmail.com
    //EJJOPIV08JNTN8UY2S7E8CY5WLRQT51E0EM1E4F1F3OYLBJKCNMV5JFU3EJ0H8R9GXA99QJ8RJ1HMW99 fins 19 setembre, queden 800 credits
    //RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G fins 21 setembre 5000 credits
    //Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP fins al 15 Agost 1000 credits
    // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $29 per 250000/mes a https://app.scrapingbee.com/
    //la principal es aquesta sota eloi@dc-image.com
    //curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&url=https%3A%2F%2Fwww.instagram.com%2F'.$username.'%2F&premium_proxy=true&render_js=false&country_code=us');
    curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key='.SCBEE_API_KEY.'&url=https%3A%2F%2Fwww.instagram.com%2F'.$username.'%2F&premium_proxy=true&render_js=false&country_code=us');

    
    
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
    // $insta_source = file_get_contents('https://www.instagram.com/explore/tags/'.$tag.'/'); // instagrame tag url
     $ch = curl_init();

    // set url
     //EJJOPIV08JNTN8UY2S7E8CY5WLRQT51E0EM1E4F1F3OYLBJKCNMV5JFU3EJ0H8R9GXA99QJ8RJ1HMW99 fins 19 setembre, queden 800 credits
    //Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP fins 15 Agost 1000 credits
     // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $99 per 1000000/mes a https://app.scrapingbee.com/
     //test nou amb scinstapi@gmail.com // digital36

    //curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=RF35LPF7MQU1RTTXSPHDXB5L490CLU9N6MKZRBZ3AU486LQLXO893CRWOZ6V1AQWN8OCGJJWA7LGKU5G&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F&premium_proxy=true&render_js=false&country_code=us');
    curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key='.SCBEE_API_KEY.'&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F&premium_proxy=true&render_js=false&country_code=us');

    
    // set method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



    // send the request and save response to $insta_source
    $insta_source = curl_exec($ch);

    // stop if fails
    if (!$insta_source) {
      echo "RT#NO";
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



function getSavedInstaPhotosAll($word='',$type='', $pais){
    //deixem parametre pais per si volguessim filtrar en un futur
    global $igRepository;

    return $igRepository->getSavedInstaPhotosAll($word, $type, $pais);
}


function getSavedInstaPhotosNumber($word='',$type='', $pais, $other){
    
    global $igRepository;

    return $igRepository->getSavedInstaPhotosNumber($word, $type, $pais, $other);
}

function saveIGPhotoDB($id_photo, $word, $path, $type, $pais, $numLikes, $text, $fbid){
    //TODO: Aqui un select a InstagramPhotoViewed per id=idPhoto
    //1.- si existeix incrementem +1 numcount
    //2.- si no existeix guarda id=$id_photo, numPrint=0, numCount=1, word=$word, type=$type, path=$path, downloadDate=, numlikes

    //3.-com controlarem quines enviem a cada petició segons el numero que ens en demanin??
    global $igRepository;

    return $igRepository->insertPhotoViewed($id_photo, $word, $path, $type, $pais, $numLikes, $text, $fbid);
}

function updateIGPhotoDB($id_photo, $word, $path, $type, $pais, $numLikes, $numPrint, $numCount, $text){
    
    global $igRepository;

    return $igRepository->updatePhotoViewed($id_photo, $word, $path, $type, $pais, $numLikes, $numPrint, $numCount, $text);
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

//suma a les que ja tenim
function updateDownloadedPhotos($token, $lastPhotosServed){
    
    global $igRepository;

    return $igRepository->updateDownloadedPhotos($token, $lastPhotosServed);
}

//fa update del valor que volguem. L'utilitzem per reiniciar a 0
function updateLastDownloadedPhotos($token, $lastPhotosServed){
    
    global $igRepository;

    return $igRepository->updateLastDownloadedPhotos($token, $lastPhotosServed);
}



function updateAllPhotosServed($token, $value){
    
    global $igRepository;

    return $igRepository->updateAllPhotosServed($token, $value); 
}

function updateInstagramUserType($token, $value){

    global $igRepository;

    return $igRepository->updateInstagramUserType($token, $value); 
}


