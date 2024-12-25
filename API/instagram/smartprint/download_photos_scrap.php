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
//require_once '../../../common/conexio_mysqli.php'; //canvia quan passis a mysqli
require_once '../../../common/conexio.php';
// require_once __DIR__ . '/../../../vendor/autoload.php'; // change path as needed

try {   
    $igRepository = new PDOInstagramRepository();    
     
    guardAgainstMissingParameters();
   
    $word = getInputWord();
   
    $downloaded_photos = 0;
    
    
  /*****************COMENÇA SCRAB*****************************************************/
    $typeRequest =  getInputType();  
    $pais = getInputPais(); 
        
         if($typeRequest=="hashtag"){
            $results_hash_array = scrape_insta_hash($word);
            $results_user_array = array();
        }else{
            $results_user_array = scrape_insta_user($word);
            $results_hash_array = array();
        }
print "<pre>";      
print_r($results_user_array);

                    $image_hash_array_likes = array();
                    $image_user_array_likes = array();
                    $fp = createImagesDirectory($word);
                    print "222222";
                    $photos_number_hashtag = count($results_hash_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges']);
print "33333";                    
//Afegim les de hashtag
                    for ($i=0; $i < $photos_number_hashtag; $i++) { 
                        print "entra hash";
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
               
                            krsort($image_hash_array_likes);


                        }
                    }

 print "44444";
                    $photos_number_hashtag = count($image_hash_array_likes); //les reals, sense video

                    if($typeRequest=='hashtag'){
                        $totalPhotos = $photos_number_hashtag;

                    }

 print "555555";


//                 if($typeRequest=='username' && isset($results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['fbid'])){
//
//                     $fbid = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['fbid'];
//                     $numFollowers = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['edge_followed_by']['count'];  
//                     $isVerified = $results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['is_verified'];
//                     if($isVerified==''){
//                        $isVerified = 0; 
//                     }
//                    
//                     $fp_fbid = createImageProfileDirectory($fbid);  
//                    fputs($fp_fbid, "{$results_user_array['entry_data']['ProfilePage']['0']['graphql']['user']['profile_pic_url_hd']}\n"); 
//                    
//                    downloadPhotoProfileInBackground($fbid,$token);
//
//                            // PHOTO_DOMAIN . IMAGES_PATH . "/{$fbid}/$fbid.jpg"; //també funcionaria
//                    $fbidImg =  PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto={$fbid}&w={$fbid}&ext=jpg&tkn={$token}";
//                    $profileVars = $fbidImg."|".$isVerified."|".$numFollowers;
//
//
//
//
//
//                 }else{
                     $fbid = 0;
                     $profileVars = 0;
//                 }   
 print "66666";
                 $photos_number_username = count($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges']);  
                    //afegim les de username 
                 print "photos username".$photos_number_username;
                    for ($i=0; $i < $photos_number_username; $i++) { 
                        print "entra";
                        if(isset($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]) && $results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]['node']['is_video']!=1 && !empty($results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'])){

                            $latest_user_array = $results_user_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]['node'];         

                            if ($i > 0) {
                                    $urls .= "|";
                                }
                               
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
                        print "{$image['id']}|{$image['image_url']}|{$image['image_likes_count']}\n";
                        //Guardem les photos a BD i despres les marcarem com a downloaded desde download_photos.php
                        $type = "username"; 
                        $id_photo = $image['id'];
                        $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$word}/$id_photo.jpg";
                        $numLikes = $image['image_likes_count'];
                        $text =  $image['image_text'];  
                        $existsPhoto = getPhotoByIdCountryType($id_photo, $pais, $type);
                        
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

               

   print "holahola";
    
  /****************ACABA SCRAB*******************************************************/  
     
    $file_content = getPhotoIds($word);
    //print_r($file_content);exit;
    $num_photos_in_file = count($file_content);
    
    utils::log("download_photos_scrab: num_photos_in_file: $num_photos_in_file", LOG_FILE);

    //de moment =0, les tirem totes de cop
    $index=0;  
    $numhash = $_GET['numhash'];
 
    
    // utils::log("download_photos_scrab: photos_number: $photos_number", LOG_FILE);

   
    for($i = $index; $i < $num_photos_in_file; $i++){
        $file_explode = explode("|",$file_content[$i]);
        
        $id_photo = str_replace(PHP_EOL, '', $file_explode[0]);
        
        utils::log("id_photo: $id_photo at: {$word}", LOG_FILE);
        $photo = str_replace(PHP_EOL, '', $file_explode[1]);
        $numLikes = $file_explode[2];
        $text = serialize($file_explode[2]);

       
       
        if(isset($photo)) {
            //Descarreguem la foto
            $img =  $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH . "/{$word}/$id_photo.jpg";
            
            utils::log("download_photos_scrab: img: $img", LOG_FILE);
         // print "eeeeooo".$id_photo.$word.$type;
            downloadIGPhoto($img, $photo);







            if($i<=$numhash){
                $type = "hashtag";
            }else{
                $type = "username";
            } 
           
            $existsPhoto = getPhotoById($id_photo);
            
            if($existsPhoto){  
                     
                updateIGPhotoDownload($id_photo);
            }          
            



        }
        
    }
   
} catch(Exception $e){    
    utils::log("download_photos_scrab: ". getInputToken(). " error: {$e->getMessage()}", LOG_FILE);
    echo $e->getMessage();
}

function createImagesDirectory($token){
    $fp = NULL;
    if(isset($token)){
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token");
        }
        //Descomentar per a acumular totes les imatges que hem anat descarregant
        //if(!file_exists($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH."/$token/$token.txt")) {
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "w"); //el crea de nou, elimina contingut
        //}else{
          
            //$fp = fopen($_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$token/$token.txt", "a+"); //no elimina el contingut, afegeix al final.
        //}    
    }
    return $fp;
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
function getInputType(){
    if (array_key_exists('type', $_GET) && !empty($_GET['type'])) {
        return $_GET['type'];
    }
    return "";
}
function getInputPais(){
    if (array_key_exists('pais', $_GET) && !empty($_GET['pais'])) {
        return $_GET['pais'];
    }
    return "";
}

function getPhotoIds($word) {
    $filename = $_SERVER["DOCUMENT_ROOT"] . IMAGES_PATH . "/{$word}/{$word}.txt";
    if (!file_exists($filename)) {
        throw new Exception("download_photos_scrab: missing $filename");
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



//SCRAPE INSTA
function scrape_insta_user($username) {
    // $insta_source = file_get_contents('https://www.instagram.com/'.$username.'/'); // instagram user url
    $ch = curl_init();

    // set url   
    // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $29 per 250000/mes a https://app.scrapingbee.com/
    //la principal es aquesta sota eloi@dc-image.com
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

function updateIGPhotoDB($id_photo, $word, $path, $type, $pais, $numLikes, $numPrint, $numCount, $text){
    
    global $igRepository;

    return $igRepository->updatePhotoViewed($id_photo, $word, $path, $type, $pais, $numLikes, $numPrint, $numCount, $text);
}

