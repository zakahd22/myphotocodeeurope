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
        echo "OK#02"; //KO abans, deixem OK#0 perque no peti el PB
    } else {
        $type = $_GET['typ'];
        $token = $_GET['tkn'];
        
        $ip = $_SERVER['REMOTE_ADDR'];

        $photos_number = $_GET['n'];
        
        $igUser = getIGUser(
            $token,
            $_GET['id']
        );
        //aqui resetejarem si canvia de type per tornar a donar contingut. 
        //Així si em demanen l'altre type quan han acabat de demanar username per exemple, tornem a inciar contador per a mostrar tot el que tinguem de hashtag. 
        //TODO: aqui un condicional to xulo que com pari $igUser type amb get type


        //quan inserta init_session per defecte a la BD type=username. si es la primera peticio i demana hashtag funciona igual 
        //perquè ha de ser igualmenttotal_photos=0 all_photos_served=0 last_downloaded_photos=0
        //i farem update perque entra al condicional
//        print $igUser->getType()."!=". $type;

        if($igUser->getType()!=$type){
//            print "entra";
            //TODO: si son diferents fer:
            //1.-Update de type
            updateInstagramUserType($token, $type);
            //2.-update de total_photos=0 all_photos_served=0 last_downloaded_photos=0
            updateTotalPhotos($token, 0);
            updateAllPhotosServed($token, 0);
            updateLastDownloadedPhotos($token, 0);
            $igUser = getIGUser(
                $token,
                $_GET['id']
            );

        }
//         print $igUser->getType()."!=". $type;
//        print $igUser->getLastDownloadedPhotos();
//        print "<pre>";
//        var_dump($igUser);exit;
        
        $other = $_GET['other'];

        if(!isset($_GET['w'])){
           
         $word = ''; 
        }else{          
         $word = $_GET['w']; //nom carpeta i arxiu .txt que guarda les fotos es la propia paraula. Cal canviar-ho?
        }    
        //si other=1 sempre retornem mentre demani, es igual si ha servit totes les fotos de la cerca, podem voler completar fins a un numero
        //si canvia la peticio de username a hashtag a dalt fem update perque AllPhotosServed=
        $typeRequest = $type;
        $pais = getCountryByIp($ip); 
        $totalPhotosInDB = getSavedInstaPhotosNumber($word, $typeRequest, $pais, $other);  
        print "total: ".$totalPhotosInDB;
        if($igUser->getAllPhotosServed() == 1 && $other==0){ 
            //Tornem a comprovar si n'hem descarregat de noves entre crida i crida i reinicialitzem se cal totalPhotos i allPhotosserved
              
            if($totalPhotosInDB>$igUser->getTotalPhotos()){
                updateAllPhotosServed($token, 0); 
                updateTotalPhotos($token, $totalPhotosInDB);
                //tornem a inicialitzar igUser
                $igUser = getIGUser(
                    $token,
                    $_GET['id']
                );
                
            }else{ //si no n'hem descarregat més i ja estàn totes servides tornem 0
                echo "OK#03";
                exit;
                
            }
        }

        
        
        
        

        $filename = $_SERVER['DOCUMENT_ROOT'] . IMAGES_PATH ."/$word/$word.txt"; //pot ser timestamp o similar
 
      $getLastDownloadedPhotos =  $igUser->getLastDownloadedPhotos();

      $numFrom = $getLastDownloadedPhotos;

      $urlsArray = getNextPhotos($word, $filename, $token, $type, $numFrom , $photos_number, $photos_number, $other, 1, $totalPhotosInDB); 
      $urls = $urlsArray['urls'];
      $urlsDownloaded = $urlsArray['urlsDownloaded'];
      $profileVars = $urlsArray['profileVars'];
//      print "<pre>";
//print_r($urlsArray);
        if(isset($urls) && $urls != "" && $totalPhotosInDB==0 ){//si ja hi ha fotos fem el download en background junt amb scrab, no aqui
            downloadNextPhotosInBackground($word,$urls, $token, $ip, $photos_number, $type);  
            
            sleep(3);
        }    
            if($urlsDownloaded==''){ //si està en blanc no tenim res descarregat anteriorment i la crida ha fallat (tampoc ha descarregat res nou)
                if($urls==''){ 
                $result = "OK#04"; 
                }else{
                    $urlsArray = getNextPhotos($word, $filename, $token, $type, $numFrom , $photos_number, $photos_number, $other, 0, $totalPhotosInDB); 
                    
                    $urlsDownloaded = $urlsArray['urlsDownloaded'];
                    $result = "OK#$urlsDownloaded#$profileVars";
                    
                    if($urlsDownloaded==''){
                        sleep(3);
                        $urlsArray = getNextPhotos($word, $filename, $token, $type, $numFrom , $photos_number, $photos_number, $other, 0, $totalPhotosInDB);               
                        $urlsDownloaded = $urlsArray['urlsDownloaded'];                       

                        $result = "OK#$urlsDownloaded"; 
                    }
                }
                
            }else{
                $result = "OK#$urlsDownloaded#$profileVars";
            }

        
    }




    
    
    utils::log("get_photos $result", LOG_FILE);
    echo $result;
} catch( Exception $e){    
    utils::log("get_photos " . getInputToken() . " error: ". $e->getMessage(), LOG_FILE);
    echo "OK#05";  //KO#{$e->getMessage()} abans, deixem OK#0 perque no peti el PB
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



function updateLastPhoto($token, $last_photo){
    global $igRepository;

    $igRepository->updateLastPhoto($token, $last_photo);
}

function getNextPhotos($word, $filename, $token, $typeRequest, $numFrom, $numTo, $photos_number, $other=0, $scrap=1, $totalPhotosInDB) {
    
    
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
   
    $numPhotosHashDownloaded = 0;     
    $numPhotosUserDownloaded = 0;  
    $ip = $_SERVER['REMOTE_ADDR'];

    $pais = getCountryByIp($ip);  
    
    
    
   
//    print  $totalPhotosInDB;
    //si ja tenim dades fem scrab i baixem en background
    if($numFrom==0 && $scrap==1 && $totalPhotosInDB){ 
        downloadNextPhotosInBackgroundScrab($word,$urls,$token,$pais,$typeRequest);
        
    }  
    
    //si no es la primera peticio que fa per aquest token no cal tornar a fer crida a Instagram consumint credits
    if($numFrom==0 && $scrap==1 && $totalPhotosInDB==0){ //forcem que no ho faci mai, despres ESBORRA
        
         if($typeRequest=="hashtag"){
            $results_hash_array = scrape_insta_hash($word);
            $results_user_array = array();
        }else{
            $results_user_array = scrape_insta_user($word);
            $results_hash_array = array();
        }
      


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
 //--------------------------------------fins aqui scrapingbee, no el fem si numFrom > 0 o scrap=0---------------------------------------------------------------   
    
    
    $arrayDownloadedPhotos = getSavedInstaPhotos($word, $typeRequest, $pais, $numFrom, $numTo, $other); 

    //Si $lastPhotosServed<$photos_number ha d'agafar les de tots els paisos
    //repetirem la linea anterior passant parametre pais=''. A la funció, si país = '' els retorna tots.
    $lastPhotosServed = count($arrayDownloadedPhotos);
    if($lastPhotosServed<$photos_number){
        $paisBuit='';
        $arrayDownloadedPhotos = getSavedInstaPhotos($word, $typeRequest, $paisBuit, $numFrom, $numTo, $other); 
        $lastPhotosServed = count($arrayDownloadedPhotos);
    }


   
    $numPhotosDownloaded = $lastPhotosServed; 
   
    $countFbid = 0;
    foreach($arrayDownloadedPhotos as $imageDownloaded){
        $urlsDownloaded .= PHOTO_DOMAIN . "/API/instagram/smartprint/photo.php?idphoto=".$imageDownloaded['id']."&w=".$imageDownloaded['word']."&ext=jpg&tkn={$token}|jpg|".$imageDownloaded['numLikes']."|".$imageDownloaded['id']."|".base64_encode($imageDownloaded['photoText'])."|";
        
        //Si no es la primera petició, en que fem scraping cal afegir el perfil de usuari si es el tenim
        if(!$countFbid && $imageDownloaded['fbid'] && !$profileVars && !$other){ //no n'hem trobat cap i fbid no es 0. I no hem assignat $profileVars quan fem scraping
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
    
    
             
    //aquesta funcio suma a les que ja tenim en fer update  
    //UPDATE InstagramUsers SET last_downloaded_photos = last_downloaded_photos +:lastPhotosServed WHERE token = :toke
    updateDownloadedPhotos($token, $lastPhotosServed);
   
    
    utils::log("getNextPhotos: photos_number: $photos_number", "eloi");
   
    if($urls != "") {
        $arrayUrls['urls'] = "$photos_number_hashtag#$photos_number_username#$urls";
        $arrayUrls['profileVars'] = $profileVars;

    }
   

    if($urlsDownloaded != "") {
          print $urlsDownloaded;
        $urlsDownloaded = substr($urlsDownloaded, 0, -1); //eliminem l'ultim |
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
         
        if(!$countMatches  && !$other){  
            
            
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
    
    $igUser = $igRepository->findByToken($token); 

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

    return $igRepository->insertSuggestion_test($word, $print, $type, $pais, $numFollowers, $isVerified, $fbid);
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

function downloadNextPhotosInBackground($word,$urls,$token,$ip,$n,$type){

    $countTypes = explode("#", $urls);
    
    $numuser = $countTypes[1];
    $numhash = $countTypes[0];

    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/instagram/smartprint/download_photos.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&ip={$ip}&n={$n}&type={$type}\" >>".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat  2>&1 &";    
    utils::log("downloadNextPhotos $command", "eloi");
    exec($command);
    
}
function downloadNextPhotosInBackgroundScrab($word,$urls,$token,$pais,$type){

    $countTypes = explode("#", $urls);
    
    $numuser = $countTypes[1];
    $numhash = $countTypes[0];
//print "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/instagram/smartprint/download_photos_scrap.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&pais={$pais}&type={$type}\" >>".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat  2>&1 &";    
    $command = "/usr/bin/curl -i \"".PHOTO_DOMAIN."/API/instagram/smartprint/download_photos_scrap.php?w={$word}&tkn={$token}&numuser={$numuser}&numhash={$numhash}&pais={$pais}&type={$type}\" >>".$_SERVER['DOCUMENT_ROOT']."/log/numa.dat  2>&1 &";    
    utils::log("downloadNextPhotosScrab $command", "eloi");
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
      echo "OK#01"; //KO abans, deixem OK#0 perque no peti el PB
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

function getSavedInstaPhotos($word='',$type='', $pais, $numFrom, $numTo, $other){
    
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
    return $index - ($photos_number-1);
}

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
