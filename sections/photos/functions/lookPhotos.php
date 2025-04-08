<?php
require_once "../../../common/global.php";
require_once G_PATH . "common/conexio.php";
require_once G_PATH . "common/Classes/baseController.php";
//error_log( "TO_DELETE lookPhotos 01" );
//substituir per classe.
$labels_apartat = "login";
include G_PATH.'labels.php';
include G_PATH."sections/photos/functions/gifEncoder.php";

include G_PATH . "src/myphotocode/domain/Contact.php";
include G_PATH . "src/myphotocode/domain/ContactType.php";

//error_log( "TO_DELETE lookPhotos 02" );

function instagramButton($picture, $size = null) {

    //detectar si es un mobil
    $what = "PC";
    if (preg_match("/iPhone|iPad|iPod/", $_SERVER['HTTP_USER_AGENT'])) {
        $what = "iOS";
    } elseif (preg_match("/Android/", $_SERVER['HTTP_USER_AGENT'])) {
        $what = "Android";
    }

    $return = "";
    if (isset($size)) {
        $redimensionar = "width = $size";
    } else {
        $redimensionar = "";
    }

    switch ($what) {
        case "iOS":
            $return = <<<HTML
                <a class='links_share iOSInstaShare' href='$picture' download><img class="photoButton" $redimensionar onclick="window.location = 'instagram://camera'" src='images/web/icon-instagram.png' alt='Instagram iOS'></a>
HTML;
            break;
        case "Android":
            $return = <<<HTML
                <a class='links_share androidInstaShare' href='$picture' download><img class="photoButton" $redimensionar onclick="window.location = 'https://www.instagram.com'" src='images/web/icon-instagram.png' alt='Instagram Android'></a>
HTML;
            break;
        default:
            break;
    }

    return $return;
}


class lookPhotos extends baseController{
    private $error = false;
    private $retMsg = "";
    private $url = "";
    private $customer_email = "";
    private $fileType = "";
    private $emailContact = null;
    private $phoneContact = null;
    /**
     * $basic_background = Flag to execut changeBackGround()
     */
    private $basic_background;
    /**
     * have3D = Flag to know if the code have 3D strip
     */
    private $have3D = false;
    
    /**
     * isStrip = Flag to know if the code contains a photo(width>heigh) or a strip
     */
    private $isStrip = false;
    
    /**
     * isMega = Flag to know if the code contains a photo(width<heigh) or a mega
     */
    private $isMega = false;
    
    /**
     * haveVideo = Flag to know if the code have video 2d
     */
    private $haveVideo = false;
    
    /**
     * haveVideo3D = Flag to know if the code have video 3d
     */
    private $haveVideo3D = false;
    
    /**
     * haveGif2D = Flag to know if the code have Gif 2D
     */
    private $haveGif2D = false;
   
    /**
     * haveQuestions = Flag to know if the code have Questons
     */
    private $haveQuestions= false;
    
    /**
     * isPrivate = Flag to know if the event is private or public
     */
    private $isPrivate = false;
    private $trashed = false;
    private $compressed = false;
    private $newServer = false;
    /**
     * tipusPhoto[
     *      1 => tira vertical,
     *      2 => tira horitzontal,
     *      3 => 4x6 horitzontal,
     *      4 => 4x6 vertical
     *      5 => 6x9 vertical,
     *      6 => 6x9 horitzontal
     * ]
     */
    private $tipusPhoto = 0;
    private $img;
    private $img3D;
    private $code;
    private $event;
    private $eventDate;
    private $background_id;
    private $video;
    private $video3D;
    private $gif;
    private $path_common_imag;
    private $eventTitle;
    private $title_date;
    private $eventDateFormat;
    private $photoDate;
    private $folder_event_3D;
    private $event_folder;
    private $hashtags;
    private $videoExtension;
    private $background_url;
    private $banner;
    private $banner_url;
    private $QR;
    private $photo_id;
    private $pbs_id;
    private $dongle_id;
    
    /** 
     * Questions vars
     */
    private $question_number = array();
    private $question = array();
    private $reply1 = array();
    private $reply2 = array();
            
    private $vist;
    private $boothType;
    private $clientType;
    private $isMobile;

    public function __construct(){
        parent::__construct();
        
        $this->createModel('events');
        $this->createModel('photos');
        $this->createModel('photo_Files');
        $this->createModel('gestor');
        $this->createModel('CLD_questions');
        $this->createModel('event_backgrounds');
        $this->createModel('CLD_emailsText');
        $this->createModel('CLD_estadistiques_photos');
        $this->createModel('CLD_Servers');
        $this->createModel('App_booths');
        $this->clientType = $this->getClientTypeFromUserAgent();
        $this->isMobile = $this->clientType == "iOS" || $this->clientType == "Android";
    }

    public function indexAction(){       
        $r = $this->checkParams();
        switch ($r){
            case 'popupView':
                $event = $_POST['i']; 
                $fileType = $_POST['t'];                
                $url = $_POST['u'];   
                
                echo $this->getPopupEmails($url, $fileType, $event);
                break;
            
            case 'popupMail':
                $this->event    = $_POST['i'];
                $this->url      = $_POST['u'];
                $this->code     = $_POST['c'];
                /**
                 * Photo or Video with uppercase
                 */
                $this->fileType       = $_POST['t'];                
                $this->customer_email = $_POST['e'];
                

                echo $this->popupMailAction();
                break;
            
            case 'saveStd':
                $typeInfo   = $_POST['t'];
                $imgCode    = $_POST['c'];
                
                echo $this->saveStatics($typeInfo, $imgCode);
                break;
            
            case 'changePublicPhoto':
                $this->prepareView();        
        
                $newPhoto_Html = "";
                $newPhoto_Html .= $this->getHead();
                $newPhoto_Html .= $this->get_photo_content();
                
                echo $newPhoto_Html;
                
                $this->saveStatics();
                
                break;
            
            case 'lookPhotos':
            default:
                $this->QR = $_REQUEST['qr'];
                $this->vist = $_REQUEST['v'];
                $this->viewAction();
                break;
        }
    }
    
    
    public function checkParams(){
        $r = $_POST['f'];
        return $r;
    }
    
    /**
     * Send Mail
     * 
     * @param type $url
     */
    public function popupMailAction(){
        $mail_ret = false;
        $result = "ERROR";
        $msg = "Unknow Error";
        $subject = "";
        $message = "";
        $text1 = "";
        $text2 = "";
        $hashtags2 = array();
        $hashtags3 = "";
        $nameOfLocation = "";
        $fileSend = "";
        $typeInfo = 4;
        
        //Get all data for email
        $hashtagsSQL = $this->eventsModel->getInfoEventSendPhoto($this->event);
        if ($hashtagsSQL) {
            $hashtags = $hashtagsSQL[0]['hashtag'];
            if($hashtags != NULL){
                $hashtags2 = explode("#", $hashtags);
            }
            $eventTitle = $hashtagsSQL[0]['title'];
        }       
        $subjectSQL = $this->CLD_emailsTextModel->getCLD_emailsText(0, $this->event);
        if($subjectSQL){
            $subject = $subjectSQL[0]['text'];
            if($subject == ""){
                $subject = "Hey, take a look at this #PoV# that I took at a DC Photobooth.";
            }
        }
        else{
            $subject = "Hey, take a look at this #PoV# that I took at a DC Photobooth.";
        }
        $text1SQL = $this->CLD_emailsTextModel->getCLD_emailsText(1, $this->event);
        if($text1SQL){
            $text1 = $text1SQL[0]['text'];
            if($text1 == ""){
                $text1 = "Check this out!  I took this #PoV# at a DC Photobooth.";
            }
        }
        else{
            $text1 = "Check this out!  I took this #PoV# at a DC Photobooth.";
        }
        $text2SQL = $this->CLD_emailsTextModel->getCLD_emailsText(2, $this->event);
        if($text2SQL){
            $text2 = $text2SQL[0]['text'];
            if($text2 == ""){
                $text2 = "Come visit our DC Photobooth.";
            }
        }
        else{
            $text2 = "Come visit our DC Photobooth.";
        }
        
        foreach($hashtags2 as $my_hashtag){
            $hashtags3 .= "<a href='https://facebook.com/hashtag/".$my_hashtag."'>#".$my_hashtag."</a> ";
        }
        // End get data
        // Replace #PoV# marks
        if($this->fileType == "photo"){
            $subject = str_replace("#PoV#", "photo", $subject);
            $text1 = str_replace("#PoV#", "photo", $text1);
            $text2 = str_replace("#PoV#", "photo", $text2);
            //$fileSend = "YourPhoto.jpg";
            $typeInfo = 4;
        }
        else if($this->fileType == "gif"){
            $subject = str_replace("#PoV#", "gif", $subject);
            $text1 = str_replace("#PoV#", "gif", $text1);
            $text2 = str_replace("#PoV#", "gif", $text2);
            //$fileSend = "YourPhoto.gif";
            $typeInfo = 4;
        }
        else if($this->fileType == "video"){
            $subject = str_replace("#PoV#", "video", $subject);
            $text1 = str_replace("#PoV#", "video", $text1);
            $text2 = str_replace("#PoV#", "video", $text2);
            //$fileSend = "YourVideo.mp4";
            $typeInfo = 7;
        }
        // End Replace
        try {
            require_once G_PATH . "common/mail.php";
            $mail= new mail();
            $mail->addAdress($this->customer_email, $this->customer_email);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/photo_send.html");
            $mail->addTemplateField("#text1#", $text1);
            $mail->addTemplateField("#text2#", $text2);
            $mail->addTemplateField("#hastags#", $hashtags3);
            if($this->fileType != "video"){
                $mail->addTemplateField("#urlFile#", '<img src="'.G_PAGE . $this->url.'" />');
            }
            else {
                $mail->addTemplateField("#urlFile#", 'You can find your video in the mail attachments.');
            }
            $mail->addTemplateField("#nameOfLocation#", ($nameOfLocation != ""?'at '. $nameOfLocation:""));
            $mail->applyTempplateFields();
            $mail->addAttachment(G_PATH . $this->url);
            if(!$mail->send()){
                $mail_ret = false;
//20250117smtp                utils::log($mail->retMsg, "logMail", "lookPhotos");
                utils::log($mail->retMsg, "logMail", "lookPhotos {$this->customer_email}");//20250117smtp
                $msg = "Can not send this Email.<br />
                        Please check if your Email is correct or try again later.";
            }
            else {
                $this->saveStatics($typeInfo, $this->code);
                $mail_ret = true;
                $this->createModel('registre_emails');
                $this->entity->loadEntity('registre_emails');
                $this->entity->setValue('email', $this->customer_email);
                $this->entity->setValue('event_id', $this->event);
                if($this->registre_emailsModel->insertRegistreEmail()){
                    if($this->fileType == "photo"){
                        $msg = "The photo has been sent, thank you!";
                    }
                    if($this->fileType == "gif"){
                        $msg = "The gif has been sent, thank you!";
                    }
                    if($this->fileType == "video"){
                        $msg = "The video has been sent, thank you!";
                    }
                }
                else{
                    $msg = "Email sent!";
                }
            }
            if($mail_ret){
                $result = 'OK';
            }
            else {
                $result = 'ERROR';
            }
            
            $array = json_encode(array('result'=>$result, 'message'=>$msg));            
            return $array;
        } catch (Exception $e){
            $array = json_encode(array('result'=>'ERROR', 'message'=>$e));
            return $array;
        }
    }
    
    public function prepareView(){
        $this->getCode();
        $this->getPhotoValues();
        if($this->getEventValues()){
            $this->getElements();

            if($this->trashed === FALSE && $this->newServer == 0){
                if($this->compressed){
                    $this->uncompress_event();
                }

                $photo_url = G_PATH . $this->event_folder . $this->code . ".jpg";

                $photoExist = file_exists ($photo_url);

                if ($photoExist === FALSE){
                    $this->error = true;
                    $this->retMsg = "#Error It has been an error loading this photo, please contact the Photobooth's owner";
                }

                $this->prepareTitle();
                $this->prepareBanner();
                $this->get_img_type();
                $this->haveVideos();
                $this->have3D();
                $this->haveGif();
                $this->haveQuestions();
                $this->background();
                
            }
            else{
                $this->error = true;
                $this->retMsg = "#Error The photo you are looking for has expired. Sorry for the inconvenience";
            }
        }
    }
    
    public function viewAction(){
        $this->prepareView();
        
        if(!$this->error){ 
            echo $this->get_view();
            $this->saveStatics();
        }
        else{ 
            echo $this->retMsg;
        }
    }
    
    public function saveStatics($typeInfo = false, $imgCode = false){
        require_once G_PATH . "common/Classes/StatisticsController.php";
        $std = new StatisticsController();
        
        $ip = $std->getIpUser();
        $v = $_REQUEST['v'];
        if(!$typeInfo){
            if ($this->QR == 1) {//QR foto
                $typeInfo = 1;
            } 
            else if($v == 2){//SMS
                $typeInfo = 15;
            }
            else if($v == 3){//Telegram
                $typeInfo = 16;
            }           
            else if($v == 4){//Email
                $typeInfo = 17;
            }
            else if($v == 5){//NFC
                $typeInfo = 18;
            }
            else if($v == 6){//QR TS
                $typeInfo = 19;
            }
            /*21-D-03-Total-Share-Whatsapp*/
            /*TODO: El typeInfo per a Whatsapp, no l'hem afegit a la taula statistics_types perque desde el 15 que ja no estan entrats... cal?*/
            else if($v == 7){//Whatsapp
                $typeInfo = 20;
            }
            else {
                $typeInfo = 2;
            }
        }
        
        if(!$imgCode){
            $imgCode = $this->code;
        }
        $std->saveStdLookPhotos($typeInfo, $imgCode, $ip);
    }
    
    public function getCode(){
        
        
        $this->code = null;
        if(isset($_GET['photocode'])){
            $this->code = trim(strtoupper($_GET['photocode']));
            $_SESSION['photoCode'] = $this->code;
            $banned = $this->banned_photo($_GET['photocode']);
            
            
            if($banned == 1){
                $this->error = true;
                $this->retMsg = "#Error ERROR 666";
            } 
        
            
        }
        else{
            $this->error = true;
            $this->retMsg = "#Error 00: No code received";
            exit();
        }
    }
    
    public function getPhotoValues(){
        $photo = $this->photosModel->getPhoto($this->code);
        if($photo){
            $flag = $photo[0]['flag'];
            $this->photoDate = $photo[0]['Appusr_datetime'];
            
            $this->photo_id     = $photo[0]['id'];
            $this->pbs_id       = $photo[0]['pbs_id'];
            $this->dongle_id    = $photo[0]['booth_id'];
            $this->boothType = $this->getBoothType();
            //utils::log($flag, "logLookPhotos");
            if($flag == 0){
                $this->event = $photo[0]['event_id'];
            }
            else{
                $this->error = true;
                $this->retMsg = "#Error 02: The photo is inapropiate";
            }
        } else{
            $this->error = true;
            $html = $this->getHead();
            $this->getContactFromGestor();
            $html .= $this->showDefaultPhotoNotAvailable();
            $this->retMsg = " #Error $html";
        }
        if ($this->error) {
            echo $this->retMsg;
        }
    }

    private function getEventValues(){
        $result = false;
        $event = $this->eventsModel->getEvent($this->event);
        if($event){
            $result = true;
            
            $this->eventDate = $event[0]['start_date'];
            $this->title_date = substr($this->eventDate, 0, 4) . "-" . substr($this->eventDate, 4, 2) . "-" . substr($this->eventDate, 6, 2);
            $this->eventTitle = $event[0]["title"];
            $owner = $event[0]["rental_id"];
            $this->isPrivate = $event[0]["private"];
            $this->background_id = $event[0]["background_id"];
            $this->compressed = $event[0]["compressed"];
            $this->HASTAGS = $event[0]["hashtag"];
            $this->HASTAGS = str_replace(" ", "", $this->HASTAGS);
            $this->HASTAGS = str_replace("#", "+%23", $this->HASTAGS);
            $sty = "";
            $this->banner = $event[0]['CLD_banner'];
            //$this->banner_url = $event[0]['CLD_banner_URL'];
            $this->banner_url =($event[0]['CLD_banner_URL'] == NULL? 'https://www.digital-centre.com' : $event[0]['CLD_banner_URL']);
            $this->trashed = ($event[0]['trashed'] == NULL? FALSE : TRUE);
            $this->newServer = $event[0]["newServer"];
        }
        else{
            $this->error = true;
            $this->retMsg = "#Error 03: Sorry, Internal error, contact with the owner of this event";
        }
        
        return $result;
    }
    
    public function getElements(){
        $this->path_common_imag = "images/web";
        $this->event_folder = "events/" . $this->eventDate . $this->event . "/";
        $this->folder_event_3D = "events/" . $this->eventDate . $this->event . "/" .$this->code. "-3D/";
        $this->img = $this->event_folder . $this->code . ".jpg";
        $this->img3D = $this->event_folder . $this->code . "-T3D.gif";
        $this->video = $this->event_folder . $this->code;
        $this->video3D = $this->event_folder . $this->code . "-3D.mp4";
        $this->gif = $this->getGifFileName();
        /* mirar de filtrar per contingut del nom dels fitxers  *$codeGIF.gif*/
    }

    private function getGifFileName(){
      $suffix = "GIF.gif";
      if($this->boothType=='V') {
          $suffix = "-PBS3.gif";
      }
      return $this->event_folder . $this->code . $suffix;
    }

    public function get_img_type(){
        
        $wImg = 0;
        $hImg = 0;
        
        list($wImg, $hImg) = getimagesize(G_PATH .$this->img);
        //segons les mides tindrem el tipus de foto 1:tira vertical   2: tira horitzontal   3:4x6 horitzontal   4:4x6 vertical
        ////deixem un marge en les comparacions
        if($this->boothType == 'V') {
            $this->tipusPhoto = 4;
        } else if($this->boothType == 'W'){// B5, que mostri com la strip mentre no ajustem les mides
            //TODO: quan sapigues les mides 
            //if(abs($wImg - 708) < 50){
            //else if(abs($wImg - 708) < 50){ //posa aqui la amplada
            //TODO: Aplicar regla general a tots els PB, no només la B5. Quan fem el projecte 20-D-16   MPC no mostra bé les fotos perquè tenen una mida diferent a Britta 9550   
            //De moment ho provem a la B5 per a arreglar el problema que té que no es mostren bé i no calcula bé el GIF            
            $factor = $wImg / $hImg;
            if($factor < 0.5) {
                    $this->tipusPhoto = 1;
            } else if($factor < 1.0) {
                    $this->tipusPhoto = 4;
            } else if($factor < 2.0) {
                    $this->tipusPhoto = 3;
            } else if($factor < 3.0) {
                    $this->tipusPhoto = 2;
            } else if($factor < 4.0) {
                    $this->tipusPhoto = 5;
            } else{
                    $this->tipusPhoto = 6;
            }
            

            //$this->tipusPhoto = 1;
        } else if((abs($wImg - 1050) < 50) && (abs($hImg - 708) < 50)) {//
            $this->tipusPhoto = 3;
        } else if(abs($wImg - 708) < 50){//
            $this->tipusPhoto = 1;
        } else if(abs($hImg - 708) < 50){//
            $this->tipusPhoto = 2;
        } else if(abs($hImg - 1416) < 50){//
            $this->tipusPhoto = 3;
        } else if(abs($wImg - 1416) < 50){//
            $this->tipusPhoto = 4;
        } else if(abs($wImg - 1864) < 50 && (abs($hImg - 1228) < 50)) {
            $this->tipusPhoto = 3;
        }
        else if(abs($wImg - 1870) < 50 && (abs($hImg - 2730) < 50)) {
            $this->tipusPhoto = 5;
        }
        else if(abs($wImg - 2740) < 50 && (abs($hImg - 1870) < 50)) {
            $this->tipusPhoto = 6;
        }
        
    }

    public function uncompress_event(){
        $descompres_gif = FALSE;
        $descompresStrip3dGif = FALSE;
        
        $_zip = new ZipArchive();
        $result = $_zip->open(G_PATH . "events/compressed_events/".$this->event."_compressed.zip");
        if ($result === TRUE) {            
            $array_compressed = array();
            
            for( $i = 0; $i < $_zip->numFiles; $i++ ){ 
                $stat = $_zip->statIndex( $i );
                
                if(strpos($stat['name'], $this->code) !== FALSE || strpos($stat['name'],'banner') !== FALSE){
                    array_push($array_compressed, $stat['name']);
                }
            }
            
            $descompres_gif = array_search("/" . $this->code . "GIf.gif", $array_compressed);
            $descompresStrip3dGif = array_search("/" . $this->code . "-T3D.gif", $array_compressed);
            
            if($descompres_gif && !$descompresStrip3dGif){
                $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-S1.jpg", $array_compressed);
                if($remove !== FALSE) unset($array_compressed[$remove]);
                $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-S2.jpg", $array_compressed);
                if($remove !== FALSE) unset($array_compressed[$remove]);
                $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-S3.jpg", $array_compressed);
                if($remove !== FALSE) unset($array_compressed[$remove]);
            }
            
            else if($descompresStrip3dGif && !$descompres_gif){
                $j = 0;
                for($i=1; $i<14; $i++){
                    $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-T$j.jpg", $array_compressed);
                    if($remove !== FALSE) unset($array_compressed[$remove]);
                    $j++;
                }
            }
            
            else{
                for($i=1; $i<14; $i++){
                    $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-T$i.jpg", $array_compressed);
                    if($remove !== FALSE) unset($array_compressed[$remove]);
                }

                $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-S1.jpg", $array_compressed);
                if($remove !== FALSE) unset($array_compressed[$remove]);
                $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-S2.jpg", $array_compressed);
                if($remove !== FALSE) unset($array_compressed[$remove]);
                $remove = array_search('/' .$this->code . "-3D/" . $this->code ."-S3.jpg", $array_compressed);
                if($remove !== FALSE) unset($array_compressed[$remove]);
            }
            
            
            foreach($array_compressed as $compressed_file){
                if(!$_zip->extractTo(G_PATH . "events/" . $this->eventDate . $this->event . "/", array($compressed_file))){
                    utils::log("ERROR descompressing", "logZip");  
                    utils::log($compressed_file, "logZip");  
                }
            }
            
            $_zip->close();
            
            
            $array = array('CLD_pDelete' => 1);
            $this->photosModel->updatePhoto($this->code, $array); 
        }  else {
            utils::log("ERROR descompressing {$this->code} from event {$this->event}: " . print_r($result, 1), "logZip");
        }     
    }
    
    public function prepareTitle(){
        if ($this->photoDate == NULL) {
            $this->eventDateFormat = substr($this->eventDate, 4, 2) . "/" . substr($this->eventDate, 6, 2) . "/" . substr($this->eventDate, 0, 4);
            $this->eventDateFormat = date("F d, Y", strtotime($this->eventDateFormat));
        } 
        else {
            $p = explode(" ", $this->photoDate);
            $this->photoDate = $p[0];
            $pDate = str_replace("-", "", $this->photoDate);
            $pDate = trim($pDate);
            $this->eventDateFormat = substr($pDate, 4, 2) . "/" . substr($pDate, 6, 2) . "/" . substr($pDate, 0, 4);
            $this->eventDateFormat = date("F d, Y", strtotime($this->eventDateFormat));
        }
    }
    
    public function prepareBanner(){
        $this->path_common_imag = "images/web";
        $this->event_folder = "events/" . $this->eventDate . $this->event . "/";
        
        if ($this->banner== 1) {
            if($this->banner_url){$this->baner .= "<a href='{$this->banner_url}' target='_blank'>";}

            if(file_exists(G_PATH . $this->event_folder ."banner.jpg")){
                $this->baner .="<img class='banner_img' src='{$this->event_folder}/banner.jpg'>";
            }
            elseif(file_exists(G_PATH . $this->event_folder ."banner.gif")){
                $this->baner .="<img class='banner_img' src='{$this->event_folder}/banner.gif'>";
            }
            
            if($this->banner_url){$this->baner .= "</a>";}
        } elseif($this->banner== 0){
            $this->baner .= "<a href='{$this->banner_url}' target='_blank'>";
            $this->baner .= "<img class='banner_img' src='{$this->path_common_imag}/banners/banner-default.gif'";
            $this->baner .= "</a>";
        }
    }

    public function haveVideos(){
        if(file_exists(G_PATH . $this->video.".wmv")){
            $this->videoExtension = ".wmv";
            $this->video = $this->video . $this->videoExtension;
            $this->haveVideo = true;
        }
        elseif(file_exists(G_PATH . $this->video.".mp4")){
            $this->videoExtension = ".mp4";
            $this->video = $this->video . $this->videoExtension;
            $this->haveVideo = true;
        }
        
        if(file_exists(G_PATH . $this->video3D)){
            $this->haveVideo3D = true;
        }
    }
    
    public function haveQuestions(){      
        
        if($_SESSION["{$this->event}_aproved"] == 0){
            $questions = $this->CLD_questionsModel->getQuestions($this->event);

            $this->num_of_questions = count($questions);
            if($this->num_of_questions > 0){
                $this->haveQuestions = true;

                foreach($questions as $question){
                    array_push($this->question_number, $question["question_number"]);
                    array_push($this->question, stripslashes($question["question"]));
                    array_push($this->reply1, stripslashes($question["reply1"]));
                    array_push($this->reply2, stripslashes($question["reply2"]));
                }
            }
        }
    }
     
    public function haveGif(){
        
        if(file_exists(G_PATH . $this->gif)){
            $this->haveGif2D = true;
//20250111gifEncoderFalla INICI
//        } elseif ($this->tipusPhoto == 1 || $this->tipusPhoto == 2){
//            $foto = imagecreatefromjpeg(G_PATH . $this->img);
//            if($this->tipusPhoto == 1){
//                list($w, $h) = getimagesize(G_PATH . $this->img);
//
//                
//                $h_m = ($h/4) - 57;
//                //TODO: Farem un calcul de $h/4 a la tira i calculem els 4 valors. De moment ho provem a la B5
//                if($this->boothType == 'W'){
//                    $h2 = Array(57,498,939,1380); 
//                }else{
//                    
//                   
//                 $h2 = Array(57,566,1075,1584);
//                }
//                
//                
//                $w -= 110;
//                $z=0;
//                while($z<4){
//                    $destino = imagecreatetruecolor($w, $h_m);
//                    $destino_name = G_PATH . $this->event_folder.$this->code."-S".$z.".gif";
//                    imagecopyresampled($destino, $foto , 0, 0, 55, $h2[$z] , $w, $h_m , $w, $h_m);
//                    imagegif($destino,$destino_name);
//                    imagedestroy($destino);
//                    $z++;
//                }
//            }   
//            elseif($this->tipusPhoto == 2){
//                list($w, $h) = getimagesize(G_PATH . $this->img);
//
//                $w_m = ($w/4) - 65;
//                $w2 = Array(75,587,1095,1600);                
//                $h -= 110;
//                $z=0;
//                while($z<4){
//                    $destino = imagecreatetruecolor($w_m, $h);
//                    $destino_name = G_PATH . $this->event_folder.$this->code."-S".$z.".gif";
//                    imagecopyresampled($destino, $foto, 0, 0, $w2[$z] , 55,  $w_m, $h , $w_m, $h);   
//                    imagegif($destino,$destino_name);       
//                    imagedestroy($destino);
//                    $z++;
//                }
//            }
//            
//            $sd = scandir(G_PATH . $this->event_folder);
//            $tempus = Array(75,50,50,50); 
//            $i=0;
//            
//            foreach ($sd as $s){
//                if ( $s != "." && $s != ".." ) {
//                    if(strpos($s,$this->code."-S") !== false) {
//                        $frames2[] = G_PATH . $this->event_folder.$s;
//                        $time2[] = $tempus[$i];
//                        $i++;
//                    }
//                }
//            }
//            
//            $gif = new GIFEncoder($frames2, $time2, 0, 2, 0, 0, 0, "url");
//            
//            if(FWrite(FOpen(G_PATH . $this->event_folder.$this->code."GIF.gif", "wb" ), $gif->GetAnimation()) > 0){
////                utils::log('Generated GIF', "logLookPhotos", "Generate Gif");
//                $exist = $this->photo_FilesModel->getFile($this->code."GIF.gif");
////                utils::log('Seaching file in Photo_files: '.$this->code."GIF.gif", "logLookPhotos", "Generate Gif");
//                if(!$exist){
////                    utils::log('Creating value', "logLookPhotos", "Generate Gif");
//                    $server_id = $this->CLD_ServersModel->getCLD_Servers('1and1');
//                    $server_id = $server_id[0]['id'];
//                    
//                    $this->entity->loadEntity('photo_Files');
//                    $this->entity->setValue("photoId", $this->photo_id);
//                    $this->entity->setValue("ServerId", $server_id);
//                    $this->entity->setValue("name", $this->code."GIF.gif");
//                    $this->entity->setValue("path", $this->event_folder.$this->code."GIF.gif");
//                    $this->entity->setValue("fileType", "gif");
//                    $this->entity->setValue("fileSize", filesize(G_PATH . $this->event_folder . $this->code . "GIF.gif"));
//                    $this->entity->setValue("photobooth", $this->pbs_id);
//                    $this->entity->setValue("dongle", $this->dongle_id);
//                    $this->entity->setValue("date", utils::get_datetime());
//                    
////                    utils::log($this->entity->getAllValues(), "logLookPhotos", "Generate Gif");
//                    
//                    if(!$this->photo_FilesModel->insertphoto_Files()){
//                        utils::log('Not inserted', "logLookPhotos", "Generate Gif");
//                    }
////                    else {
////                        utils::log('Inserted', "logLookPhotos", "Generate Gif");
////                    }
//                }
//            }
//            
//            $x=0;
//            while($x<$i){
//                unlink(G_PATH . $this->event_folder.$this->code."-S".$x.".gif");
//                $x++;
//            }
//
//            $this->haveGif2D = true;
//20250111gifEncoderFalla FINAL
        }
    }
    
    public function have3D(){
        if (file_exists(G_PATH.$this->folder_event_3D) 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T1.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T2.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T3.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T4.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T5.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T6.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T7.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T8.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T9.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T10.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T11.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T12.jpg") 
        && file_exists(G_PATH.$this->folder_event_3D . $this->code . "-T13.jpg")){
            
            $_x = 1;
            while ($_x < 14){
                $imageObject = imagecreatefromjpeg(G_PATH . $this->folder_event_3D . $this->code . "-T$_x.jpg");
                $imageObject2 = imagecreatetruecolor(354, 1076);
                $w = imagesx($imageObject);
                $h = imagesy($imageObject);
                imagecopyresampled($imageObject2, $imageObject , 0, 0, 0,0 , 354, 1076, $w, $h);
                imagegif($imageObject2, G_PATH . $this->folder_event_3D . $this->code . "-T$_x.gif");
                $_x++;
            }

            $_x = 1;
            while ($_x < 14){
                $frames[] = G_PATH . $this->folder_event_3D . $this->code . "-T$_x.gif";
                $time[] = 10;
                $_x++;
            }
            $_x = 13;
            while ($_x > 0){
                $frames[] = G_PATH . $this->folder_event_3D . $this->code . "-T$_x.gif";
                $time[] = 10;
                $_x--;
            }
            $gif = new GIFEncoder($frames, $time, 0, 2, 0, 0, 0, "url");
            
            if(FWrite(FOpen(G_PATH . $this->event_folder . $this->code . "-T3D.gif", "wb" ), $gif->GetAnimation()) > 0){
//                utils::log('Generated GIF', "logLookPhotos", "Generate Gif");
                $exist = $this->photo_FilesModel->getFile($this->code."-T3D.gif");
//                utils::log('Seaching file in Photo_files: '.$this->code."-T3D.gif", "logLookPhotos", "Generate Gif");
                if(!$exist){
//                    utils::log('Creating value', "logLookPhotos", "Generate Gif");
                    $server_id = $this->CLD_ServersModel->getCLD_Servers('1and1');
                    $server_id = $server_id[0]['id'];
                    
                    $this->entity->loadEntity('photo_Files');
                    $this->entity->setValue("photoId", $this->photo_id);
                    $this->entity->setValue("ServerId", $server_id);
                    $this->entity->setValue("name", $this->code."-T3D.gif");
                    $this->entity->setValue("path", $this->event_folder.$this->code."-T3D.gif");
                    $this->entity->setValue("fileType", "gif");
                    $this->entity->setValue("fileSize", filesize(G_PATH . $this->event_folder . $this->code . "-T3D.gif"));
                    $this->entity->setValue("photobooth", $this->pbs_id);
                    $this->entity->setValue("dongle", $this->dongle_id);
                    $this->entity->setValue("date", utils::get_datetime());
                    
//                    utils::log($this->entity->getAllValues(), "logLookPhotos", "Generate Gif");
                    
                    if(!$this->photo_FilesModel->insertphoto_Files()){
                        utils::log('Not inserted', "logLookPhotos", "Generate Gif");
                    }
//                    else {
//                        utils::log('Inserted', "logLookPhotos", "Generate Gif");
//                    }
                }
            }
            
            $_x = 1;
            while ($_x < 14){
                unlink(G_PATH . $this->folder_event_3D . $this->code . "-T$_x.gif");
                $_x++;
            }
            
            $this->have3D = true;
        }
        elseif (file_exists(G_PATH . $this->event_folder . $this->code . "-T3D.gif")){
            $this->have3D = true;
        }
    }
    
    public function get_view(){
        $html = $this->getHead();
        $html .= '<div class="wrapper">';
            $html .= $this->getHeader();
            $html .= '<div id="photo_content">';
                $html .= $this->get_photo_content();
            $html .= '</div>';
            $html .= $this->getBanner();
        $html .= '</div>';
        $html .= "<script>change_background('{$this->background_url}')</script>";
        $html .= $this->getQuestions();
            
        return $html;
    }
    
    public function get_photo_content(){
        if($this->boothType == 'V') {
            return $this->getPhotoContentS3();
        }
        $html = $this->getPhoto();
        $html .= '<div class="second">';
            $html .= $this->getSharePhoto();
            $html .= $this->getShareVideo();
            $html .= $this->getGif();
        $html .= '</div>';
            
        return $html;
    }

    public function getPhotoContentS3(){
        if($this->isMobile){
            $html = '<div class="first_container">';
            $html .= $this->getPhoto();
            $html .= '</div>';
            $html .= "<div class='first_container_share'>";
            $html .= $this->getSharePhoto('s3_s_photo');    
            $html .= "</div>";
        } else {
            $html = '<div class="first_container">';
            $html .= $this->getPhoto();
            $html .= $this->getSharePhoto('s3_s_photo');
            $html .= '</div>';
        }
        $html .= '<div class="second s3_second">';
        $html .= $this->getShareVideo();
        $html .= $this->getGif();
        $html .= '</div>';

        return $html;
    }
    
    public function getQuestions(){
        $html = "";
            
        if($this->haveQuestions){           
//            $html .= "<div id='title_popup_lookPhoto>EventQuestions</div>";
            $title = "EventQuestions";
//            $html .= "<div id = 'content_popup_lookPhoto'>";
            $html .= "<form id='EventQuestions' onsubmit='return false;'>";
            
            $row = 1;

            for($i = 0; $i < $this->num_of_questions; $i++){
                
                if($this->question_number[$i] == 1){
                    $html .= "<p>Your e-mail:<input type='text' name='email'></p>";
                }
                else{
                    $html .= "<p>{$row}- {$this->question[$i]}</p>";
                    $html .= "<p class='ans'><input type='radio' name='q{$this->question_number[$i]}' value='1'> {$this->reply1[$i]}</p>";
                    $html .= "<p class='ans'><input type='radio' name='q{$this->question_number[$i]}' value='2'> {$this->reply2[$i]}</p>";
                    
                    $row++;
                }
                
            }
            $html .= "<p>Answer these questions to remove the popup, Thanks</p>";
//            $html .= "<div class='lookPhoto_butons'>";
//            $html .= "<input type='button'  onclick='askQuestions();' value='SEND' style='width:150px;height:30px;margin-top:10px;'>";
//            $html .= "<input type='hidden' name='event' value='{$this->event}'>";
//            $html .= "</div>";
//            $html .= "</form>";
//            $html .= "</div>";
//            $content = htmlentities($html, ENT_QUOTES);
//            $content = $html;
            
            $buttons = "";
            //$buttons .= "<div class='lookPhoto_butons'>";
            $buttons .= "<input type='button' class='popup-confirm' onclick='askQuestions();' value='SEND' style='margin-top:-10px;'>";
            $html .= "<input type='hidden' name='event' value='{$this->event}'>";
            //$buttons .= "</div>";
            $buttons .= "</form>";
            $html .= "</div>";

            $content = htmlentities($html, ENT_QUOTES);
            $content = $html;
            
//            $array = json_encode(array('title'=>$title, 'content'=>$content));
            $array = json_encode(array('title'=>$title, 'content'=>$content, 'buttons'=>$buttons));
            
            $html = "<script> showPopupQuestions({$array}); </script>";
        }
        
        return $html;
    }
    
    public function getHead(){
         $html = <<<HTML
            <meta charset="UTF-8">
            <meta http-equiv="content-type" content="text/html;charset=utf-8">
            <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
            <link href="https://fonts.googleapis.com/css?family=Paytone+One" rel="stylesheet" type="text/css">
            <link href='sections/photos/resources/css/lookPhotos.css' type='text/css' media="(min-width : 480px)" rel='stylesheet'>
            <link href="sections/photos/resources/css/lookPhotos_mv.css"  media="(max-width : 480px)" rel="stylesheet"/>
            <script src='sections/photos/resources/js/lookPhotos.js'></script>
            <script src='assets/js/facebook.js'></script>
            <script src='../../totalshare/functions.js'></script>
            <link rel="stylesheet" href="assets/libraries/font-awesome-4.7.0/css/font-awesome.min.css">

HTML;
         if($this->basic_background){
             $html .= $this->basic_background;
         }
         return $html;
    }
    
    public function getHeader(){
        $html = <<<HTML
            <div class="header">
                <div class="title">
                   <h1>$this->eventTitle</h1>
                   <h2>$this->eventDateFormat</h2>
                </div>
                
                <div class="fb-login-button likeFB" data-max-rows="1" data-size="small" data-button-type="login_with" data-show-faces="false" data-auto-logout-link="true" data-use-continue-as="false"></div> 
                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = 'https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v3.0&appId=127533357397300&autoLogAppEvents=1';
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
                </div>    
                <div class="menu">
                    <iframe style="margin-top: 30px" id='likeFB' class="likeFB" src="//www.facebook.com/plugins/like.php?locale=en_US&href=http%3A%2F%2Fwww.facebook.com%2Fdigitalcentre&amp;width=100&amp;layout=box_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=65" scrolling="no" frameborder="0"  allowTransparency="true"></iframe>
                    <div id="pos_div0"></div>
                    <div id="content_face" hidden><div id="faceBtnLog" class="fb-login-button" data-max-rows="1" data-size="medium" data-show-faces="true" onlogin="loginAction()" data-auto-logout-link="true"></div></div>
                    <div id="pos_div1"></div>
                    <img  src="images/web/back2.png" id="back_btn" onclick='toCodePhoto()'>
                </div>
            
HTML;
        return $html;
    }

    public function getPhoto(){
        
        $this->event_folder = "events/" . $this->eventDate . $this->event . "/";

        if($this->boothType == 'V'){
            $html = '<div class="s3">';
            $html .= '<img id="s3_img" src="'.$this->event_folder . $this->code.'.jpg">';
            $html .= '</div>';
        } elseif($this->tipusPhoto == 1){
            if(!$this->have3D){
                $html = '<div class="strips">';
                $html .= '<img id="strip_img" src="'.$this->event_folder . $this->code.'.jpg" style="width:205px;">';
                $html .= '</div>';
            }
            else{
                $html = <<<HTML
                    <div id="photo3dConten" onclick="setToFront();">
                        <img src="{$this->event_folder}{$this->code}.jpg" class="photoButton" id="no3dphoto" >
                        <img src="{$this->path_common_imag}/3D.png" id="band3d">
                        <img src="{$this->event_folder}{$this->code}-T3D.gif" id="d3photo">
                        <img src="{$this->path_common_imag}/2D.png" id="band">
                        <img src="{$this->path_common_imag}/f3d1.png" id="f3d">
                    </div>
                    <script>setToFront()</script>
HTML;
            }
            
        }
        elseif($this->tipusPhoto == 2){
            $html = '<div class="strips_horitzontal">';
            $html .= '<img id="strip_img_horitzontal" src="'.$this->event_folder . $this->code.'.jpg" style="width:623px;">';
            $html .= '</div>';
        }
        elseif($this->tipusPhoto == 3){
            $html = '<div class="strips_mega">';
            $html .= '<img id="strip_mega_img" src="'.$this->event_folder . $this->code.'.jpg" style="width:600px;">';
            $html .= '</div>';
        }
         elseif($this->tipusPhoto == 5){
            $html = '<div class="strips_6x9">';
            $html .= '<img id="strip_6x9_vertical" src="'.$this->event_folder . $this->code.'.jpg" style="width:600px;">';
            $html .= '</div>';
        }
         elseif($this->tipusPhoto == 6){
            $html = '<div class="strips_6x9">';
            $html .= '<img id="strip_6x9_horitzontal" src="'.$this->event_folder . $this->code.'.jpg" style="width:900px;">';
            $html .= '</div>';
        }
        
        return $html;
    }

    public function getPhotoS3(){
        $this->event_folder = "events/" . $this->eventDate . $this->event . "/";
        $html = '<div class="strips_mega">';
        $html .= '<img id="strip_mega_img" src="'.$this->event_folder . $this->code.'.jpg" style="width:600px;">';
        $html .= '</div>';
        return $html;
    }

    private function getHashtagsTwitter(){
        
        
        return str_replace(" ", ",", str_replace("#", "", trim($this->hashtags, " ")));
    }
    
    public function getPopupEmails($url, $type, $event){
        
        $title = "Insert your email";
        $content = '<input type="text" class="popup-input-large" id="email" name="email" />';
        $content .= '<div id="mailError" name="mailError"></div>';
        //loading
        $buttons = '<input type="button" class="swal2-confirm styled loading" value="Submit" onclick="sendEmail(\''.$type.'\', \''.$url.'\', \''.$event.'\')" />';
        $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();' />";

        $array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
        return json_encode($array_result);
    }

    public function getSharePhoto($sharePhotoClass = 's_photo'){
        $textTwitter = 'Look the photo I posted of the "' . $this->eventTitle . '" event!';
        $textTwitter = str_replace(" ", "+", $textTwitter);
        $urlPhotoTwitter = "https://twitter.com/intent/tweet?url=".G_PAGE. $this->img ."&text={$textTwitter}&hashtags={$this->getHashtagsTwitter()}";
        $urlPhotoTwitter = filter_var($urlPhotoTwitter, FILTER_SANITIZE_URL);
        $photo_url = G_PAGE . $this->event_folder . $this->code . ".jpg";
        
        if($this->code==="AMEG23596Z"){
            $html = <<<HTML
            <div class="share $sharePhotoClass" id="sharePhoto2D">
                <div class="cove_img"></div>
                <a href='$this->img' class="links_share" download>
                    <img class="photoButton" id="img2D" src="{$this->path_common_imag}/icon-download.png">

                </a>  
                    <img class="photoButton facebookUploadSDk" hashtags="{$this->hashtags}" code='{$this->code}' type_shared="3" fileType='photo' id="face_button_start" PhotoUrl="$photo_url" src="{$this->path_common_imag}/button-facebook.png">
                <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='photo' url='{$this->img}'>
                    <img class="photoButton" id="email2D" src="{$this->path_common_imag}/icon-mail.png">
                </a>
                <a class="links_share twitterShare" href='$urlPhotoTwitter' target='_blank' code='{$this->code}' type_shared="5">
                    <img class="photoButton" id="twitter2D"  src="{$this->path_common_imag}/icon-twitter.png">
                </a>
                <!-- VIGILAR AMB LA id = videoTwitter -->
                <input type='hidden' value='$urlPhotoTwitter' id='videoTwitter'>
            
HTML;
            
        }else{
            
            $html = <<<HTML
            <div class="share $sharePhotoClass" id="sharePhoto2D">
                <div class="cove_img"></div>
                <a href='$this->img' class="links_share" download>
                    <img class="photoButton" id="img2D" src="{$this->path_common_imag}/icon-download.png">

                </a>
                    
                <a class="links_share" href="https://www.facebook.com/dialog/feed?app_id=127533357397300&redirect_uri=http://www.myphotocode.com&link=www.myphotocode.com/index.php?code={$this->code}&picture=$photo_url">
                    <img class="photoButton facebook" id="img2D" src="{$this->path_common_imag}/button-facebook.png" style=''/>

                </a> 
                    
                   
                <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='photo' url='{$this->img}'>
                    <img class="photoButton" id="email2D" src="{$this->path_common_imag}/icon-mail.png">
                </a>
                <a class="links_share twitterShare" href='$urlPhotoTwitter' target='_blank' code='{$this->code}' type_shared="5">
                    <img class="photoButton" id="twitter2D"  src="{$this->path_common_imag}/icon-twitter.png">
                </a>
                <!-- VIGILAR AMB LA id = videoTwitter -->
                <input type='hidden' value='$urlPhotoTwitter' id='videoTwitter'>
            
HTML;
            
        }
        
        
        $html .= $this->instagramButton($this->img);
        $html .= "</div>";
        if($this->have3D){
            $urlPhotoTwitter = "https://twitter.com/intent/tweet?url=".G_PAGE. $this->img3D ."&text={$textTwitter}&hashtags={$this->getHashtagsTwitter()}";
            $urlPhotoTwitter = filter_var($urlPhotoTwitter, FILTER_SANITIZE_URL);
            $photo_url = G_PAGE . $this->event_folder . $this->code . ".gif";
            
            $html .= <<<HTML
                <div class="share s_photo3D"id="sharePhoto3D">
                    <div class="cove_img"></div>
                    <a href='$this->img3D' class="links_share" download>
                        <img class="photoButton" id="img2D"    src="{$this->path_common_imag}/downloadLook.png">
                    </a>
                    
                        <img class="photoButton facebookUploadSDk" hashtags="{$this->hashtags}" code='{$this->code}' type_shared="3" fileType='photo' id="face_button_start" PhotoUrl="$photo_url" src="{$this->path_common_imag}/button-facebook.png">
                    
                    <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='photo' url='{$this->img}'>
                        <img class="photoButton" id="email3D"    src="{$this->path_common_imag}/button-email.png">
                    </a>
                    <a href='$urlPhotoTwitter' target='_blank' class="links_share twitterShare" code='{$this->code}' type_shared="5">
                        <img class="photoButton" id="twitter2D"  src="{$this->path_common_imag}/button-twitter.png">
                    </a> 
                    <input type='hidden' value='$urlPhotoTwitter' id='videoTwitter'>
                </div>   
HTML;
        }               
        
        return $html;
    }

    public function getShareVideo(){
        $html = '';
        $textTwitter = 'Look the video I posted of the "' . $this->eventTitle . '" event!';
        $textTwitter = str_replace(" ", "+", $textTwitter);
        $photo_url = G_PAGE . $this->event_folder . $this->code . ".gif";
        
        if($this->haveVideo){
//            $textTwitter = 'Look the video I posted of the "' . $this->eventTitle . '" event!';
//            $textTwitter = str_replace(" ", "+", $textTwitter);
            $video_url = G_PAGE . $this->video;
            $urlVideoTwitter = "https://twitter.com/intent/tweet?url=".G_PAGE.$this->video."&text={$textTwitter}&hashtags={$this->getHashtagsTwitter()}";
            $urlVideoTwitter = filter_var($urlVideoTwitter, FILTER_SANITIZE_URL);
            
            $html = <<<HTML
                <div class="share s_video" id="shareVideo2D">
                    <div class="cove_img"></div>
                    <a href='$this->video' class="links_share" download>
                        <img class="photoButton" src="{$this->path_common_imag}/downloadLook.png">
                    </a>
                    <a  onClick='startPopup("video","{$this->code}|{$this->eventDate}{$this->event}");' class="links_share">
                        <img class="photoButton" id="video"    src="{$this->path_common_imag}/button-video.png">
                    </a>
                        
                    <img class="photoButton facebookUploadSDk" hashtags="{$this->hashtags}" code='{$this->code}' fileType='video' id="face_button_start_video" type_shared="6" PhotoUrl="$video_url" src="{$this->path_common_imag}/button-facebook.png">
                    
                    <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='video' url='{$this->video}'>
                        <img class="photoButton" id="email"    src="{$this->path_common_imag}/button-email.png">
                    </a>
                    <a href='$urlVideoTwitter' target='_blank' class="links_share twitterShare" code='{$this->code}' type_shared="8">
                        <img class="photoButton" id="twitter" src="{$this->path_common_imag}/button-twitter.png">
                    </a>
                    <input type='hidden' value='$urlVideoTwitter' id='videoTwitter'>
                </div>
HTML;
        }
        
        if($this->haveVideo3D){
//            $textTwitter = 'Look the video I posted of the "' . $this->eventTitle . '" event!';
//            $textTwitter = str_replace(" ", "+", $textTwitter);
            $urlVideoTwitter = "https://twitter.com/intent/tweet?url=".G_PAGE.$this->video3D."&text={$textTwitter}&hashtags={$this->getHashtagsTwitter()}";
            $urlVideoTwitter = filter_var($urlVideoTwitter, FILTER_SANITIZE_URL);
            
            $html .= <<<HTML
                <!-- <div class="share s_video3D" id="shareVideo3D"> -->
                <div class="share s_video3D" style="height:110px; width:370px;" id="shareVideo3D">
                    <div class="cove_img"></div>
                    <a href='$this->video3D' class="links_share" download>
                        <img class="photoButton" src="{$this->path_common_imag}/downloadLook.png">
                    </a>
                    <a  onClick='startPopup("video3D","{$this->code}|{$this->eventDate}{$this->event}");' class="links_share">
                        <img class="photoButton" id="video"    src="{$this->path_common_imag}/button-video.png">
                    </a>
                    <!--
                    <a href='assets/php/templates/facebook-video3D.php' class="links_share mail_links_share">
                        <img class="photoButton" id="facebook" type_shared="6" src="{$this->path_common_imag}/button-facebook.png">
                    </a>
                    -->
                    <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='Video' url='{$photo_url}'>
                        <img class="photoButton" id="email"    src="{$this->path_common_imag}/button-email.png">
                    </a>
                    <a href='$urlVideoTwitter' target='_blank' class="links_share twitterShare" code='{$this->code}' type_shared="8">
                        <img class="photoButton" id="twitter"  src="{$this->path_common_imag}/button-twitter.png">
                    </a>
                    <input type='hidden' value='$urlVideoTwitter' id='videoTwitter'>
                </div>
HTML;
        }
        return $html;
    }

    public function getGif(){
        $html = "";
        $textTwitter = 'Look the GIF I posted of the "' . $this->eventTitle . '" event!';
        $textTwitter = str_replace(" ", "+", $textTwitter);
        $urlVideoTwitter = "https://twitter.com/intent/tweet?url=".G_PAGE. $this->gif ."&text={$textTwitter}&hashtags={$this->getHashtagsTwitter()}";

        if($this->haveGif2D){
          if($this->boothType=='V') {
            return $this->getGifS3Html($urlVideoTwitter);
          }
          if($this->tipusPhoto == 1){
            return $this->getGifVerticalHtml($urlVideoTwitter);
          }
          elseif($this->tipusPhoto == 2){
            return $this->getGifHorizontalHtml($urlVideoTwitter);
          }
          elseif($this->tipusPhoto == 3){
            return $this->getGifHorizontalHtml($urlVideoTwitter);
          }
          elseif($this->tipusPhoto == 4){
            return $this->getGifHorizontalHtml($urlVideoTwitter);
          }
          elseif($this->tipusPhoto == 5){
            return $this->getGifHorizontalHtml($urlVideoTwitter);
          }
          elseif($this->tipusPhoto == 6){
            return $this->getGifHorizontalHtml($urlVideoTwitter);
          }
        }
        return $html;
    }

    private function getBoothType(){
        $this->boothType = "";
        $photoBooth = $this->App_boothsModel->getBoothId($this->pbs_id);
        if (is_array($photoBooth) && count($photoBooth) > 0) {
            if(array_key_exists("type", $photoBooth[0])) {
                $this->boothType = $photoBooth[0]['type'];
            }
        }
        return $this->boothType;
    }


    public function getBanner(){
        $html = "";
        if($this->isPrivate == 0){
            $html .="<div id='private_div'><span class='bSp' onclick='openPhotosAllPhotos($this->event);'>See all photos</span></div>";
            $html .= "<div id='popupWait'><img src='assets/images/loading.gif' width='24' height='24' /></div>";
            $html .= "<div id='popup'></div>";
            $html .= "<div id='AllPhotoDiv' style='display:none;'></div>";
            $html .= "<div id='backgroundPopup'></div></div>";
        }
        
        $html .= '<div class="'.($this->isMobile && $this->boothType=='V'?"s3_banner":"banner").'">';
        $html .= $this->baner;
        $html .= '</div>';
       
        return $html;
    }
    
    public function background(){
        /*BACKGROUND*/
        if ($this->background_id == 0) {
            $this->background_url = "assets/images/backgrounds/background-default.jpg";
        } 
        else if($this->background_id > 0 && $this->background_id != 99){
            $background = $this->event_backgroundsModel->getBackground($this->background_id);
            
            if($background){
                $color =  $background[0]['color'];
                $i =  $background[0]['image_url'];
                $image = G_PAGE . "assets/images/backgrounds/" . $background['image_url'];
                $repeat = $background[0]['repeat'];
                
                $this->basic_background = "<script>changeBackGround('$color', '$repeat')</script>";
            }
        }
        else {
            $this->background_url = "events/" . $this->eventDate . $this->event . "/background.jpg";
        }
    }
    /*desactivat temporalment buscar --!-- al document lookPhotos.php original*/
    public function url_get_contents($Url) {
        if (function_exists('curl_init')) {
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $Url);                                                                                                     
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $output = curl_exec($ch);
           curl_close($ch);
           return $output;
       }
    }

  private function getGifS3Html($urlVideoTwitter) {
    $html = <<<HTML
        <div class="s3_gif_c">
            <img class="s3_gif" src= "$this->gif">
        </div>
        <div class="share s3_share_gif s3_s_photo id="sharePhoto2D">
            <div class="cove_img"></div>
            <a href='$this->gif' class="links_share" download>
                <img class="photoButton" id="img2D" src="{$this->path_common_imag}/icon-download.png">
            </a>
            <a class="links_share" href="https://www.facebook.com/dialog/feed?app_id=127533357397300&redirect_uri=http://www.myphotocode.com&link=www.myphotocode.com/index.php?code={$this->code}&picture=$photo_url">
                <img class="photoButton facebook" id="img2D" src="{$this->path_common_imag}/button-facebook.png" style=''/>
            </a> 
            <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='photo' url='{$this->gif}'>
                <img class="photoButton" id="email2D" src="{$this->path_common_imag}/icon-mail.png">
            </a>
            <a class="links_share twitterShare" href='$urlVideoTwitter' target='_blank' code='{$this->code}' type_shared="5">
                <img class="photoButton" id="twitter2D"  src="{$this->path_common_imag}/icon-twitter.png">
            </a>
            <!-- VIGILAR AMB LA id = videoTwitter -->
            <input type='hidden' value='$urlVideoTwitter' id='videoTwitter'>
        </div>
HTML;
    return $html;
  }

  private function getGifVerticalHtml($urlVideoTwitter) {
    $html = <<<HTML
                <div class="others_container">
                    <div class="gif_container">
                        <img class="gif" src= "$this->gif">
                        <div class ="share share_gif">  
                            <div id="cover_img"></div>
                            <a href='$this->gif' class="links_share" download>
                                <img class="photoButton" src="{$this->path_common_imag}/downloadLook.png">
                            </a>
                            <a class="links_share mail_links_share" event='{$this->event}' code='{$this->code}' type='gif' url='{$this->gif}'>
                                <img class="photoButton" src="{$this->path_common_imag}/emailIMG.png">
                            </a>
                            <a href='$urlVideoTwitter' target='_blank' class="links_share twitterShare" code='{$this->code}' type_shared="5">
                                <img class="photoButton" src="{$this->path_common_imag}/button-twitter.png">
                            </a>
                        </div>
                        <input type='hidden' value='$urlVideoTwitter' id='videoTwitter'>
                    </div>
                </div>
HTML;
    return $html;
  }

  private function getGifHorizontalHtml($urlVideoTwitter) {
    $html = <<<HTML
                <div class="others_container_hor">
                    <div class="gif_container_hor">
                        <div class ="share share_gif_hor">  
                            <div id="cover_img_hor"></div>
                            <a href='$this->gif' class="links_share_hor"  download>
                                <img class="photoButton photoButton_hor" src="{$this->path_common_imag}/downloadLook.png">
                            </a>
                            <a class="links_share_hor mail_links_share" event='{$this->event}' code='{$this->code}' type='gif' url='{$this->gif}'>
                                <img class="photoButton photoButton_hor" src="{$this->path_common_imag}/emailIMG.png">
                            </a>
                            <a href='$urlVideoTwitter' target='_blank' class="links_share_hor twitterShare" code='{$this->code}' type_shared="5">
                                <img class="photoButton photoButton_hor" src="{$this->path_common_imag}/button-twitter.png">
                            </a>
                        </div>
                        <img class="gif" id="gif_hor" src= "$this->gif">
                        <input type='hidden' value='$urlVideoTwitter' id='videoTwitter'>
                    </div>
                </div>
HTML;
    return $html;
  }
//202501    function instagramButton($picture, $size) {
    function instagramButton($picture, $size = null) {


        $return = "";
        if (isset($size)) {
            $redimensionar = "width = $size";
        } else {
            $redimensionar = "";
        }

        switch ($this->clientType) {
            case "iOS":
                $return = <<<HTML
                <a class='links_share iOSInstaShare' href='$picture' download><img class="photoButton" $redimensionar onclick="window.location = 'instagram://camera'" src='images/web/icon-instagram.png' alt='Instagram iOS'></a>
HTML;
                break;
            case "Android":
                $return = <<<HTML
                <a class='links_share androidInstaShare' href='$picture' download><img class="photoButton" $redimensionar onclick="window.location = 'https://www.instagram.com'" src='images/web/icon-instagram.png' alt='Instagram Android'></a>
HTML;
                break;
            default:
                $return = "";
                break;
        }

        return $return;
    }

    private function getClientTypeFromUserAgent()
    {
        $what = "PC";
        if (preg_match("/iPhone|iPad|iPod/", $_SERVER['HTTP_USER_AGENT'])) {
            $what = "iOS";
        } elseif (preg_match("/Android/", $_SERVER['HTTP_USER_AGENT'])) {
            $what = "Android";
        }
        return $what;
    }

    public function banned_photo($code){
        $CLD_CON = getNewBdD();
            $sql = "SELECT `banned` , events.`rental_id` , photos.`event_id` FROM CLD_Login, events , photos WHERE `id_user` = events.`rental_id` AND events.`id` = photos.`event_id` AND photos.`code` = '$this->code'";
            $CLD_CON->OpenRs($sql);
            while ($CLD_CON->FetchArray()) {
                $banned = $CLD_CON->GetArrayField("banned");                
            }
        
        return $banned;
    }


    private function getContactFromGestor() {
        $codeContactList = $this->gestorModel->findByCode($this->code);
        if(isset($codeContactList)) {
          $this->emailContact = $this->findFirstEmailContact($codeContactList);
          $this->phoneContact = $this->findFirstPhoneContact($codeContactList);
        }
    }

    private function findFirstEmailContact($photoContactList) {
        if(!empty($photoContactList)) {
            foreach($photoContactList as $contact){
                if($contact['state'] !== "6" && $contact["method"] === "0"){
                    return new Contact(ContactType::EMAIL, $contact['contact']);
                }
            }
        }
        return null;
    }

    private function findFirstPhoneContact($photoContactList) {
        if(!empty($photoContactList)) {
            foreach($photoContactList as $contact){
                if($contact['state'] !== "6" && $contact["method"] === "1"){
                    return new Contact(ContactType::PHONE, $contact['contact']);
                }
            }
        }
        return null;
    }

    private function getPhotoboothLastConnection($code) {
        $rand_string = substr($code, 1, 3);

        // Query the booths table for the rand_string.
        $CLD_CON = getNewBdD();
        $sql = "SELECT id FROM booths WHERE rand_string = '$rand_string' LIMIT 1";
        $CLD_CON->OpenRs($sql);
        $boothRow = null;
        while ($CLD_CON->FetchArray()) {
            $boothRow = $CLD_CON->GetArray();
        }
        if (!$boothRow) {
            return null;
        }

        // Query App_boothDongle using the booth id.
        $idDongle = $boothRow['id'];
        $CLD_CON2 = getNewBdD();
        $sql2 = "SELECT idBooth FROM App_boothDongle WHERE idDongle = $idDongle LIMIT 1";
        $CLD_CON2->OpenRs($sql2);
        $dongleRow = null;
        while ($CLD_CON2->FetchArray()) {
            $dongleRow = $CLD_CON2->GetArray();
        }
        if (!$dongleRow) {
            return null;
        }

        // Query App_booths to obtain lastConn.
        $idBooth = $dongleRow['idBooth'];
        $CLD_CON3 = getNewBdD();
        $sql3 = "SELECT lastConn FROM App_booths WHERE idBooth = $idBooth LIMIT 1";
        $CLD_CON3->OpenRs($sql3);
        $boothsRow = null;
        while ($CLD_CON3->FetchArray()) {
            $boothsRow = $CLD_CON3->GetArray();
        }
        if (isset($boothsRow['lastConn']) && !empty($boothsRow['lastConn'])) {
            return $boothsRow['lastConn'];
        }
        return null;
    }

    private function showDefaultPhotoNotAvailable() {

        $cancelButton = '<input type="image" src="images/icons/cancel-blue_60x75.png" onclick="cancel()" value="No, thanks"><br><br>';

        // Default message if the photo is not available yet.
        $messageOutput = "The photo {$this->code} is not available yet,<br/> would you like us to notify you as soon as it is ready?<br><br>";

        $lastConn = $this->getPhotoboothLastConnection($this->code);
        if (!$lastConn) {
            // If no last connection is found, it likely means the code is invalid.
            $messageOutput = "The code you entered is invalid. Please check and try again.";
            $html = <<<HTML
                {$messageOutput}
                <div id="sendOptions">
                    {$cancelButton}
                </div>
            HTML;
            return $html;
        } else {
            // Convert lastConn to a timestamp.
            $lastConnTime = strtotime($lastConn);
            $fifteenDaysAgo = strtotime("-15 days");
            if ($lastConnTime < $fifteenDaysAgo) {
                // If the photobooth hasn’t connected for more than 15 days, indicate that.
                $messageOutput = "The photo you requested will not be available because the photobooth appears to have been offline for over 15 days. Please contact your local operator for assistance.";
                $html = <<<HTML
                    {$messageOutput}
                    <div id="sendOptions">
                        {$cancelButton}
                    </div>
                HTML;
                return $html;
            }
        }

        $html = <<<HTML
            {$messageOutput}
            <div id = "sendOptions">
                <!-- For sending SMS reminder -->
                <!-- <input type="image" src="images/icons/phone_60x75.png" onclick="avisaSMS();" id="si" value="Yes, send me a SMS"> -->
                <!-- For sending whatsapp reminder -->
                <!-- <input type="image" src="images/icons/whatsapp.png" onclick="avisaWhatsapp();" id="si" value="Yes, send me a WhatsApp"> -->
                <input type="image" src="images/icons/email_60x75.png" onclick="avisaMail();" id="si" value="Yes, send me an email">
                {$cancelButton}
            </div>
            <div id="dades">
                <!-- For sending SMS -->
                <!-- <div class="smsSend" style="display: none" id="sms">
                    {$this->getInputPhoneContactHtml()}
                </div> -->
                <!-- For sending whatsapp -->
                <!-- <div class="whatsappSend" style="display: none" id="whatsapp">
                    {$this->getInputWhatsappContactHtml()}
                </div> -->
                <div class="emailSend" style="display: none" id="mail">
                    {$this->getInputEmailContactHtml()}
                </div>
            </div>
            <div id="complet" style="display: none"> You will recieve a message when the photo is uploaded </div>
        HTML;

        if(isset($this->emailContact) && !empty($this->emailContact->getValue())) {
            setcookie("photo_contact_email", $this->emailContact->getValue(), time()+3600, "/");
        }
        // Uncomment the following lines if you want to set a cookie for the phone contact as well.
        // if(isset($this->phoneContact) && !empty($this->phoneContact->getValue())) {
        //     setcookie("photo_contact_phone", $this->phoneContact->getValue(), time() + 3600, "/");
        // }
        return $html;
    }

    private function getInputPhoneContactHtml() {
      if(isset($this->phoneContact) && !empty($this->phoneContact->getValue())) {
        $html = "<input class=\"phoneNumberWithPrefix\" type='text' id='txt' value=\"{$this->phoneContact->getValue()}\">";
      } else {
        $html = <<<HTML
            <input class="phonePrefix" type='text' id='pref' value="+34">
            <input class="phoneNumber" type='text' id='txt' placeholder=' Your SMS number'>
HTML;
      }
      $html .= "<input type='image' class=\"sendButton\"  src=\"images/icons/send_60x133.png\" onclick='envia(\"$this->code\", 1);' id='enviasms' value='send'>";
      return $html;
    }
    /*21-D-03-Total-Share-Whatsapp*/
    private function getInputWhatsappContactHtml() {
      if(isset($this->phoneContact) && !empty($this->phoneContact->getValue())) {
        $html = "<input class=\"phoneNumberWithPrefix\" type='text' id='txt' value=\"{$this->phoneContact->getValue()}\">";
      } else {
        $html = <<<HTML
            <input class="phonePrefix" type='text' id='prefwhatsapp' value="+34">
            <input class="phoneNumber" type='text' id='txtwhatsapp' placeholder=' Your Whatsapp number'>
HTML;
      }
      $html .= "<input type='image' class=\"sendButton\"  src=\"images/icons/send_60x133.png\" onclick='envia(\"$this->code\", 3);' id='enviawhatsapp' value='send'>";
      return $html;
    }

  private function getInputEmailContactHtml() {
    if(isset($this->emailContact) && !empty($this->emailContact->getValue())) {
      $html = "<input type='text' class=\"emailText\" id='txtmail' value=\"{$this->emailContact->getValue()}\">";
    } else {
      $html =  "<input type='text' class=\"emailText\" id='txtmail'  placeholder=' Your e-mail'>";
    }
    $html .= "<input type='image' class=\"sendButton\" src=\"images/icons/send_60x133.png\" onclick='envia(\"{$this->code}\", 0);' id='enviamail' value='send'>";
    return $html;
  }
}

$lookPhotos = new lookPhotos();
$lookPhotos->indexAction();


?>

