<?php

//function dbConnect() {
//
//    $localhost = "db399687929.db.1and1.com";
//    $mysql_user = "dbo399687929";
//    $mysql_password = "digitalcentre";
//    $mydb = "db399687929";
//
//    $link = mysql_connect($localhost, $mysql_user, $mysql_password);
//    if (!$link)
//        die('ko');
//    mysql_select_db($mydb);
//}

//function createNewEvent($baseController, $boot_rental_id) {
//
//    $currentDate = date('Ymd');
//    
//    $baseController->entity->loadEntity('events');
//    $baseController->entity->setValue("rental_id", $boot_rental_id);
//    $baseController->entity->setValue("start_date", $currentDate);
//    
//    $baseController->eventsModel->insertEvent();
//
//    return mysql_insert_id();
//}

//function getFtpInfo($event_id) {
//
//    $ftp_folder = mysql_fetch_array(mysql_query("SELECT * FROM ftp_folders ORDER BY RAND() LIMIT 1"));
//
//    $host = $ftp_folder['host'];
//    $user = $ftp_folder['path'];
//    $path = $ftp_folder['user'];
//    $pass = $ftp_folder['password'];
//
//    mysql_query("UPDATE ftp_folders SET ftp_folder_id=$ftp_folder[id] WHERE id=$event_id");
//
//    return $host . "#" . $user . "#" . $path . "#" . $pass;
//}

function VIC_fesLog($text) {
    if (filesize("logVIC.dat") > 5000000) {
        copy("logVIC.dat", "logVIC-".date('YmdHis')."-".rand(10,99).".bak");
        $fh = fopen("logVIC.dat", 'w');
    }
    else
        $fh = fopen("logVIC.dat", 'a');
    fwrite($fh, date('Y-m-d H:i:s ') . $text . "\n");
    fclose($fh);
}

//function mail_fesLog($text) {
//
//    $fh = fopen("logMailRest.dat", 'a');
//    fwrite($fh, date('Y-m-d H:i:s ') . $text . "\n");
//    fclose($fh);
//}
//
//function mail_addLog($text) {
//    $fh = fopen("logMailRest.dat", 'a');
//    fwrite($fh, "         *  " . $text . "\n");
//    fclose($fh);
//}

////////////////////////////////////////////////////////////////////
// GO!
////////////////////////////////////////////////////////////////////

include_once "../common/global.php";
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('booths');
$baseController->createModel('events');
$baseController->createModel('ftp_folders');
$baseController->createModel('photos');
$baseController->createModel('CLD_estadistiques_upload');
$baseController->createModel('photo_Files');
$baseController->createModel('CLD_Servers');
$baseController->createModel('App_boothDongle');
$baseController->createModel('statistics_photo_files', FALSE , 'myphotocode_statistics');


$dongle = $_REQUEST['dongle'];
$event_id = $_REQUEST['event_id'];

$photobooth_id = FALSE; 
if(isset($_REQUEST['idb'])){
    $photobooth_id = $_REQUEST['idb'];
}



VIC_fesLog("confirmupload, dongle: $dongle; event_id: $event_id"); 

$old = umask(0);

if (!$dongle) die("ko#dongle");

$booth = $baseController->boothsModel->getBooth($dongle);

$boot_rental_id = $booth[0]["rental_id"];
$boot_id        = $booth[0]["id"]; 

if (!$booth) die("ko#booth");

if (!$event_id) die("ko#event_id");

$event = $baseController->eventsModel->getEvent($event_id, $boot_rental_id);
$idEvent = $event[0]['id'];
$id_ftpFolder = $event[0]['ftp_folder_id'];
$start_S_Date = $event[0]['start_date'];

if (!$event) die("ko#event");
$file = $_REQUEST['f'];
if (!$file) die("ko#f");

$ftp_folder = $baseController->ftp_foldersModel->getftp_folder($id_ftpFolder);
$ftp_folder_path = $ftp_folder[0]['path'];

VIC_fesLog("confirmupload, dongle: $dongle; ftp_folder: $ftp_folder_path; file: $file");

$old_file = "../uploads/" . $ftp_folder_path . "/" . $file;

if (file_exists($old_file)) {
    $new_file = "../events/" . $start_S_Date . $idEvent . "/" . $file;
    if(!is_dir("../events/".$start_S_Date . $idEvent)){ mkdir("../events/".$start_S_Date . $idEvent,0777);}
    
    $file_exploded = explode(".", $file);
    $name_exploded = explode("-", $file_exploded[0]);
    if (count($name_exploded) == 2) {
        if ($name_exploded[1] == "3D" && $file_exploded[1] == "mpg") {
            $new_file = "../events/" . $start_S_Date . $idEvent . "/" . $file;
        }
        if ($name_exploded[1] == "T3D" && $file_exploded[1] == "gif") {
            $new_file = "../events/" . $start_S_Date . $idEvent . "/" . $file;
        }
        if (preg_match('/T[0-9]/', $name_exploded[1]) && $file_exploded[1] == "jpg") {
            if (!file_exists("../events/" . $start_S_Date . $idEvent . "/" . $name_exploded[0] . "-3D/")) {
                mkdir("../events/" . $start_S_Date . $idEvent . "/" . $name_exploded[0] . "-3D/", 0777);
            }    
            $new_file = "../events/" . $start_S_Date . $idEvent . "/" . $name_exploded[0] . "-3D/" . $file;
        }
        if (preg_match('/S[0-9]/', $name_exploded[1]) && $file_exploded[1] == "jpg") {
            if (!file_exists("../events/" . $start_S_Date . $idEvent . "/" . $name_exploded[0] . "-3D/")) {
                mkdir("../events/" . $start_S_Date . $idEvent . "/" . $name_exploded[0] . "-3D/", 0777);
            }
            $new_file = "../events/" . $start_S_Date . $idEvent . "/" . $name_exploded[0] . "-3D/" . $file;
        }
    }
    
    rename($old_file, $new_file) or die("ko#rename");
    chmod($new_file, 0777) or die("ko#chmod");
    
    VIC_fesLog("confirmupload, dongle: $dongle; new_file: $new_file;"); 

    $dongle = $baseController->boothsModel->getBooth($dongle);
    $id_dongle  = $dongle[0]["id"];
    
    if($photobooth_id === FALSE){
        $app_boothDongle = $baseController->App_boothDongleModel->boothPhotobooth($id_dongle, 1);
        $photobooth_id = $app_boothDongle[0]['idBooth'];
    }
    
    if ($file_exploded[1] == "jpg" && count($name_exploded) == 1) {
        $photodate = $_REQUEST['s'];
        if (!$photodate)
            $photodate = date('Y-m-d H:i');

        $myTRACE = "confirmupload, TRACE date REQUEST s: " . $_REQUEST['s']; 
        if (substr($photodate, 4, 1) != "-") {
            $photodate = substr($photodate, 0, 4) . "-" . substr($photodate, 4, 2) . "-" . substr($photodate, 6);
        }

        VIC_fesLog("$myTRACE photodate: $photodate"); 
        
        $photo = $baseController->photosModel->getPhoto($file_exploded[0]);
        
        $now = utils::get_datetime("Y-m-d H:i");
        
        $limitDate = utils::modify_date($now, '+3 days', 'Y-m-d H:i');
        
        $stdLimitDate = utils::datetime_to_date_std($limitDate, 'Y-m-d H:i', 'YmdHi');
        $stdPhotoDate = utils::datetime_to_date_std($photodate, 'Y-m-d H:i', 'YmdHi');    
        
        if($stdPhotoDate > $stdLimitDate){ 
            utils::log("===== PB id={$photobooth_id} DATETIME ERROR ======", "logConfirmUpload", "confirmupload");
            utils::log("{$stdPhotoDate} > {$stdLimitDate}", "logConfirmUpload", "confirmupload");
            $photodate = $now;
            utils::log("--------------------", "logConfirmUpload", "confirmupload");
            utils::log("NEW DATE = {$photodate}", "logConfirmUpload", "confirmupload");
            utils::log("=========== END PB DATETIME ERROR ===============", "logConfirmUpload", "confirmupload");
        }

        if (!$photo) {
            $baseController->entity->loadEntity('photos');
            $baseController->entity->setValue("code", $file_exploded[0]);
            $baseController->entity->setValue("event_id", $event_id);
            $baseController->entity->setValue("booth_id", $id_dongle);
            $baseController->entity->setValue("Appusr_datetime", $photodate);
            $baseController->entity->setValue("pbs_id", $photobooth_id);
            $photo_id = $baseController->photosModel->insertPhoto();
            
            if(!$photo_id) die("ko#insert");           
            $datess = date("Y-m-d");
            $array = array('CLD_date_lastPhoto' => $datess);
            $baseController->eventsModel->updateEvent($event_id, $array);
            
            $update = array('photoId' => $photo_id);
            if(!$baseController->photo_FilesModel->updatePhoto_Files_CodePhoto($file_exploded[0], $update)){
                utils::log("Photo_Files not Uploaded. CodePhoto: {$file_exploded[0]}", "logConfirmUploadError");
            }
        }        
        
    }
    
    //////////////////////////////////////////////////////
    // GUARDAR ESTADISTIQUES A BD
    //////////////////////////////////////////////////////
    
    $server_id = $baseController->CLD_ServersModel->getCLD_Servers('1and1');
    $server_id = $server_id[0]['id'];
    
    $photobooth_id = $baseController->App_boothDongleModel->boothPhotobooth($id_dongle);
    $photobooth_id = $photobooth_id[0]['idBooth'];

    $fileAdded = substr($new_file, 3);
    
    $baseController->entity->loadEntity('photo_Files');
    $baseController->entity->setValue("photoId", $photo_id);
    $baseController->entity->setValue("ServerId", $server_id);
    $baseController->entity->setValue("name", $file);
    $baseController->entity->setValue("path", $fileAdded);
    $baseController->entity->setValue("fileType", $file_exploded[1]);
    $baseController->entity->setValue("fileSize", filesize($new_file));
    $baseController->entity->setValue("photobooth", $photobooth_id);
    $baseController->entity->setValue("dongle", $id_dongle);
    $baseController->entity->setValue("date", utils::get_datetime());

/*
//    utils::log("INSERT VALUES \n"
//            . "photoId: {$photo_id} \n"
//            . "ServerId: {$server_id} \n"
//            . "name: {$file} \n"
//            . "path: {$new_file} \n"
//            . "fileType: {$file_exploded[1]} \n"
//            . "fileSize: filesize($new_file) \n"
//            . "photobooth: {$photobooth_id} \n"
//            . "dongle: {$id_dongle} \n"
//            . "date: ".utils::get_datetime()." \n",
//        "logConfirmUpload");
*/
    
    if(!$baseController->photo_FilesModel->insertphoto_Files()) die("ko#insert");

    $date = utils::get_datetime("Y-m-d");
    $baseController->CLD_estadistiques_uploadModel->increase_CLD_estadistiques_upload($date);
    
    $photo_files = $baseController->statistics_photo_filesModel->getStd_photoFile($boot_rental_id, $date);
    
    if(count($photo_files) > 0){
        
        $photoFile_id = $photo_files[0]["id"];
        $nFiles = $photo_files[0]["nFiles"];
        $nFiles = $nFiles + 1;
        
        $update = array('nFiles' => $nFiles);
        
        $upd = $baseController->statistics_photo_filesModel->updateStd_photoFiles($photoFile_id, $update);
    }
    else{
        $baseController->entity->loadEntity('statistics_photo_files');
        $baseController->entity->setValue("owner", $boot_rental_id);
        $baseController->entity->setValue("date", $date);
        $baseController->entity->setValue("nFiles", 1);
        $insert = $baseController->statistics_photo_filesModel->insertStd_photoFiles();
    }
    
    //////////////////////////////////////////////////////
    // EMAIL (MEGA OUT)
    //////////////////////////////////////////////////////
    $email = $_REQUEST['email'];
    
    if (isset($email)) {
        $baseUrl = "https://www.myphotocode.com";
        $photoDir =  G_PATH . "events/" . $start_S_Date . $idEvent;
        $imgUrl = $photoDir . "/" . $file_exploded[0] . ".jpg";

        $to = $email;
        $to_str = "";  

        $mail_retMsg = "";
        ob_start();
 
        require_once(G_PATH . 'common/mail.php');

        $mail = new mail();
        
        $mail->addAdress($to, $to_str);
        $mail->setSubject("What a great picture");
        
        $mail->setTemplate(G_PATH . "common/resources/templates/html/en/rest.html");
        
        $mail->addTemplateField("#PHOTO#", "$baseUrl/includes/emails/getPhoto.php?f=".$event['start_date'] . $idEvent."&cdf=" . $file_exploded[0]);
        $mail->addTemplateField("#COUPON#", "$baseUrl/includes/emails/img/couponBase.jpg");
        $mail->applyTempplateFields();
        
        if($mail->addAttachment($imgUrl, "YourPhoto.jpg")){ if (!$mail->Send()) { }}
        
        $mail_retMsg = ob_get_contents();
        ob_end_clean();
        if($mail->ret){ utils::log($mail->retMsg, "logMail", "confirmupload");}
    }
    VIC_fesLog("confirmupload, dongle: $dongle; ok");
 
    die("ok");
} 
else {die("ko#file");}

umask($old);

if ($old != umask()) {die("ko#An error occurred while changing back the umask");}
